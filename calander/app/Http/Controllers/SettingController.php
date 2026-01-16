<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
class SettingController extends Controller
{
    public function __construct()
    {
        // no-op constructor — ImageManager and GD removed per requirements
    }

    /* ===============================
     * SHOW SETTINGS PAGE
     * =============================== */
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        $sliders = array_filter(
            $settings,
            fn ($v, $k) => preg_match('/^slider\d+$/', $k),
            ARRAY_FILTER_USE_BOTH
        );

        uksort($sliders, fn ($a, $b) =>
            (int) filter_var($a, FILTER_SANITIZE_NUMBER_INT)
            <=>
            (int) filter_var($b, FILTER_SANITIZE_NUMBER_INT)
        );

        // Build public URLs for any stored images so the view can render previews.
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        $base = request()->getSchemeAndHttpHost();
        $imageUrls = [];
        foreach ($settings as $k => $v) {
            if (!$v) {
                $imageUrls[$k] = '';
                continue;
            }
            $publicPath = ltrim($v, '/');
            // Build URL using the current request host so previews work when accessing by IP
            $imageUrls[$k] = $base . '/storage/' . $publicPath;
        }

        $sliderInitialUrls = [];
        foreach ($sliders as $k => $v) {
            if (!$v) {
                $sliderInitialUrls[$k] = '';
                continue;
            }
            $publicPath = ltrim($v, '/');
            $sliderInitialUrls[$k] = $base . '/storage/' . $publicPath;
        }

        return view('backend.logo.index', compact('settings', 'sliders', 'imageUrls', 'sliderInitialUrls'));
    }

    /* ===============================
     * SAVE SETTINGS
     * =============================== */
    public function store(Request $request)
    {
        Log::info('Settings form submitted', [
            'fields' => array_keys($request->all()),
            'files' => array_keys($request->allFiles()),
            'ip' => $request->ip()
        ]);

        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'contact_address' => 'nullable|string|max:255',

            'logo_color' => 'nullable|regex:/^#([0-9a-fA-F]{6})$/',
            'logo_color_hex' => 'nullable|regex:/^#([0-9a-fA-F]{6})$/',

            'logo_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_logo_image' => 'nullable|boolean',
        ]);

        foreach (['site_name', 'contact_phone', 'contact_email', 'contact_address'] as $key) {
            Setting::setValue($key, $validated[$key] ?? null);
        }

        $color = $validated['logo_color'] ?? $validated['logo_color_hex'] ?? null;
        Setting::setValue('logo_color', $color);
        Setting::setValue('logo_color_hex', $color);

        // Invalidate caches first, we'll repopulate after changes.
        Cache::forget('settings');
        Cache::forget('sliders');

        try {
            $this->handleLogo($request);
            $this->handleSliders($request);

            // Rebuild caches for quick access elsewhere in the app
            $settingsArr = Setting::pluck('value', 'key')->toArray();
            Cache::put('settings', $settingsArr);

            $sliders = array_filter(
                $settingsArr,
                fn ($v, $k) => preg_match('/^slider\d+$/', $k),
                ARRAY_FILTER_USE_BOTH
            );
            uksort($sliders, fn ($a, $b) =>
                (int) filter_var($a, FILTER_SANITIZE_NUMBER_INT)
                <=>
                (int) filter_var($b, FILTER_SANITIZE_NUMBER_INT)
            );
            Cache::put('sliders', $sliders);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation errors so they display properly
            Log::warning('Validation failed', [
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\RuntimeException $e) {
            Log::error('Runtime error during settings save', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()
                ->route('admin.events.logo')
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Unexpected error during settings save', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()
                ->route('admin.events.logo')
                ->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()])
                ->withInput();
        }

        Log::info('Settings saved successfully');
        return redirect()
            ->route('admin.events.logo')
            ->with('success', 'Settings saved successfully');
    }

    /* ===============================
     * LOGO HANDLER
     * =============================== */
    private function handleLogo(Request $request): void
    {
        $key = 'logo_image';
        $existing = Setting::getValue($key);

        if ($request->boolean('remove_logo_image')) {
            if ($existing) Storage::disk('public')->delete($existing);
            Setting::setValue($key, null);
            Cache::forget('settings');
            return;
        }

        if (!$request->hasFile($key)) return;

        if ($existing) Storage::disk('public')->delete($existing);

        $path = $this->processImage($request->file($key));

        Setting::setValue($key, $path);
        // cache will be rebuilt by caller
    }

    /* ===============================
     * SLIDER HANDLER
     * =============================== */
    private function handleSliders(Request $request): void
    {
        $allSettings = Setting::pluck('value', 'key')->toArray();
        $keys = [];

        foreach (array_merge(array_keys($request->all()), array_keys($request->allFiles())) as $key) {
            if (preg_match('/^(remove_)?slider\d+$/', $key)) {
                $keys[] = str_replace('remove_', '', $key);
            }
        }

        $keys = array_unique($keys);

        $rules = [];
        foreach ($keys as $key) {
            $rules[$key] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048';
            $rules['remove_' . $key] = 'nullable|boolean';
        }

        try {
            Validator::make($request->all(), $rules)->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors for debugging
            Log::warning('Slider validation failed', [
                'errors' => $e->errors(),
                'sliders' => $keys
            ]);
            throw $e;
        }

        foreach ($keys as $key) {
            $existing = $allSettings[$key] ?? null;

            if ($request->boolean('remove_' . $key)) {
                Log::info("Removing slider: {$key}");
                if ($existing) Storage::disk('public')->delete($existing);
                Setting::deleteKey($key);
                continue;
            }

            if (!$request->hasFile($key)) continue;

            try {
                Log::info("Processing slider: {$key}", [
                    'original_name' => $request->file($key)->getClientOriginalName(),
                    'size' => $request->file($key)->getSize(),
                    'mime' => $request->file($key)->getMimeType()
                ]);

                if ($existing) Storage::disk('public')->delete($existing);

                $path = $this->processImage($request->file($key));

                Setting::setValue($key, $path);

                Log::info("Slider saved successfully: {$key}", ['path' => $path]);
            } catch (\Exception $e) {
                Log::error("Failed to process slider: {$key}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw new \RuntimeException("The {$key} failed to upload: " . $e->getMessage());
            }
        }

        $this->compactSliders();
        // cache will be rebuilt by caller
    }

    /* ===============================
     * DEPLOYMENT-SAFE IMAGE HANDLER
     * (GD only | PNG fallback if JPEG not supported)
     * =============================== */
    private function processImage($file): string
    {
        // Simplified: store uploaded file as-is under `settings/` on the public disk.
        // This removes the need for Intervention/Image and GD extensions.
        Log::info('Processing image upload', [
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType()
        ]);

        if (!$file) {
            throw new \RuntimeException('No file provided');
        }

        if (!$file->isValid()) {
            $error = $file->getError();
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini (currently 2MB)',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive in HTML form',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by PHP extension',
            ];
            $errorMsg = $errorMessages[$error] ?? 'Unknown upload error (code: ' . $error . ')';

            Log::error('File upload invalid', [
                'error_code' => $error,
                'error_message' => $errorMsg,
                'file' => $file->getClientOriginalName()
            ]);

            throw new \RuntimeException('Upload failed: ' . $errorMsg);
        }

        $ext = $file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg';
        $filename = uniqid('s_') . '.' . $ext;
        $path = 'settings/' . $filename;

        // Store file in public disk
        try {
            Storage::disk('public')->putFileAs('settings', $file, $filename);

            Log::info('Image saved successfully', [
                'path' => $path,
                'filename' => $filename
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save image to storage', [
                'error' => $e->getMessage(),
                'path' => $path
            ]);
            throw new \RuntimeException('Failed to save file: ' . $e->getMessage());
        }

        return $path;
    }

    /* ===============================
     * COMPACT SLIDERS (slider1..N)
     * =============================== */
    private function compactSliders(): void
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $sliders = [];

        foreach ($settings as $key => $value) {
            if (preg_match('/^slider(\d+)$/', $key, $m)) {
                $sliders[(int) $m[1]] = $value;
            }
        }

        ksort($sliders);

        foreach ($settings as $key => $_) {
            if (preg_match('/^slider\d+$/', $key)) {
                Setting::deleteKey($key);
            }
        }

        $i = 1;
        foreach ($sliders as $path) {
            if ($path) {
                Setting::setValue('slider' . $i, $path);
                $i++;
            }
        }
    }
}
