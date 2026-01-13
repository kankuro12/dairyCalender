<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $announcements = Announcement::query()
            ->orderByDesc('priority')
            ->orderByDesc('start_at')
            ->orderByDesc('id')
            ->get();

        $editing = null;
        $editId = $request->query('edit');
        if ($editId) {
            $editing = Announcement::query()->find($editId);
        }

        return view('backend.announcement.index', compact('announcements', 'editing'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        Announcement::query()->create($validated);

        Cache::forget('announcement_bar');

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $this->validated($request);

        $announcement->update($validated);

        Cache::forget('announcement_bar');

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        Cache::forget('announcement_bar');

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'nep_title' => 'nullable|string|max:255',
            'type' => 'required|in:news,announcement,event,alert',
            'priority' => 'nullable|integer',
            'status' => 'nullable|boolean',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        $validated['priority'] = (int) ($validated['priority'] ?? 0);
        $validated['status'] = (bool) ($validated['status'] ?? false);

        return $validated;
    }
}
