<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    /**
     * Display news slider on public page
     */
    public function getNewsForSlider()
    {
        $cacheKey = 'news_slider';

        $news = Cache::rememberForever($cacheKey, function() {
            return News::active()
                ->published()
                ->byPriority()
                ->limit(15)
                ->get();
        });

        return $news;
    }

    /**
     * Show single news detail page
     */
    public function show($id)
    {
        $news = News::active()->findOrFail($id);

        return view('calendar.news.show', compact('news'));
    }

    /**
     * Admin: List all news
     */
    public function index(Request $request)
    {
        $news = News::query()
            ->byPriority()
            ->paginate(20);

        $editing = null;
        $editId = $request->query('edit');
        if ($editId) {
            $editing = News::find($editId);
        }

        return view('backend.news.index', compact('news', 'editing'));
    }

    /**
     * Admin: Store new news
     */
    public function store(Request $request)
    {
        $validated = $this->validateNews($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->handleImageUpload($request->file('image'));
        }

        // Parse sources JSON if provided
        if ($request->filled('sources_json')) {
            try {
                $validated['sources'] = json_decode($request->sources_json, true);
            } catch (\Exception $e) {
                $validated['sources'] = null;
            }
        }

        News::create($validated);

        // Clear all relevant caches
        Cache::forget('news_slider');
        Cache::flush(); // Clear all cache to ensure fresh data

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'News created successfully');
    }

    /**
     * Admin: Update existing news
     */
    public function update(Request $request, News $news)
    {
        $validated = $this->validateNews($request);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $validated['image'] = $this->handleImageUpload($request->file('image'));
        }

        if ($request->boolean('remove_image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $validated['image'] = null;
        }

        // Parse sources JSON if provided
        if ($request->filled('sources_json')) {
            try {
                $validated['sources'] = json_decode($request->sources_json, true);
            } catch (\Exception $e) {
                $validated['sources'] = null;
            }
        }

        $news->update($validated);

        // Clear all relevant caches
        Cache::forget('news_slider');
        Cache::flush(); // Clear all cache to ensure updated news shows immediately

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'News updated successfully');
    }

    /**
     * Admin: Delete news
     */
    public function destroy(News $news)
    {
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        // Clear all relevant caches
        Cache::forget('news_slider');
        Cache::flush(); // Clear all cache to ensure deleted news is removed immediately

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'News deleted successfully');
    }

    /**
     * Admin: Fetch news from external API
     */
    public function fetchFromApi(Request $request)
    {
        $request->validate([
            'api_url' => 'required|url',
            'api_key' => 'nullable|string',
        ]);

        try {
            $headers = [];
            if ($request->filled('api_key')) {
                $headers['Authorization'] = 'Bearer ' . $request->api_key;
            }

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->get($request->api_url);

            if ($response->failed()) {
                return back()->withErrors(['api_error' => 'Failed to fetch news from API']);
            }

            $data = $response->json();

            // Process and save news items
            // This structure depends on your API format
            $imported = 0;

            if (isset($data['articles']) && is_array($data['articles'])) {
                foreach ($data['articles'] as $article) {
                    News::create([
                        'title' => $article['title'] ?? '',
                        'title_nep' => $article['title_nep'] ?? null,
                        'excerpt' => $article['description'] ?? null,
                        'content' => $article['content'] ?? null,
                        'image' => $article['image'] ?? null,
                        'source_url' => $article['url'] ?? null,
                        'source_name' => $article['source']['name'] ?? 'API',
                        'source_type' => 'api',
                        'published_at' => $article['publishedAt'] ?? now(),
                        'status' => true,
                    ]);
                    $imported++;
                }
            }

            Cache::forget('news_slider');

            return back()->with('success', "Successfully imported {$imported} news items");

        } catch (\Exception $e) {
            Log::error('News API fetch failed', [
                'error' => $e->getMessage(),
                'url' => $request->api_url
            ]);

            return back()->withErrors(['api_error' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Validate news data
     */
    private function validateNews(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'title_nep' => 'nullable|string|max:255',
            'excerpt' => 'nullable|string',
            'excerpt_nep' => 'nullable|string',
            'content' => 'nullable|string',
            'content_nep' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'source_url' => 'nullable|url',
            'source_name' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'priority' => 'nullable|integer',
            'published_at' => 'nullable|date',
            'remove_image' => 'nullable|boolean',
        ]);
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload($file): string
    {
        $filename = uniqid('news_') . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('news', $filename, 'public');
    }
}
