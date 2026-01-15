<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;


class SettingController extends Controller
{
    protected $image;

    public function __construct()
    {
        // Use Imagick if available, otherwise fallback to GD
        // Imagick is preferred as it has better format support
        if (extension_loaded('imagick')) {
            $this->image = new ImageManager(\Intervention\Image\Drivers\Imagick\Driver::class);
        } else {
            // Check GD JPEG support before using GD driver
            $gdInfo = gd_info();
            $hasJpegSupport = isset($gdInfo['JPEG Support']) && $gdInfo['JPEG Support'];

            if (!$hasJpegSupport) {
                // If GD doesn't have JPEG support, we'll need to handle this in processImage
                \Log::warning('GD library does not have JPEG support. Some image formats may not work correctly.');
            }

            $this->image = new ImageManager(\Intervention\Image\Drivers\Gd\Driver::class);
        }
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

        return view('backend.logo.index', compact('settings', 'sliders'));
    }

    /* ===============================
     * SAVE SETTINGS
     * =============================== */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'contact_address' => 'nullable|string|max:255',

            'logo_color' => 'nullable|regex:/^#([0-9a-fA-F]{6})$/',
            'logo_color_hex' => 'nullable|regex:/^#([0-9a-fA-F]{6})$/',

            'logo_image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'remove_logo_image' => 'nullable|boolean',
        ]);

        foreach (['site_name', 'contact_phone', 'contact_email', 'contact_address'] as $key) {
            Setting::setValue($key, $validated[$key] ?? null);
        }

        $color = $validated['logo_color'] ?? $validated['logo_color_hex'] ?? null;
        Setting::setValue('logo_color', $color);
        Setting::setValue('logo_color_hex', $color);

        Cache::forget('settings');
        Cache::forget('sliders');

        try {
            $this->handleLogo($request);
            $this->handleSliders($request);
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.events.logo')
                ->withErrors(['image' => $e->getMessage()])
                ->withInput();
        }

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

        $path = $this->processImage(
            $request->file($key),
            300, // logo width
            90   // logo quality
        );

        Setting::setValue($key, $path);
        Cache::forget('settings');
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
            $rules[$key] = 'nullable|image|mimes:jpg,jpeg,png|max:4096';
            $rules['remove_' . $key] = 'nullable|boolean';
        }

        Validator::make($request->all(), $rules)->validate();

        foreach ($keys as $key) {
            $existing = $allSettings[$key] ?? null;

            if ($request->boolean('remove_' . $key)) {
                if ($existing) Storage::disk('public')->delete($existing);
                Setting::deleteKey($key);
                continue;
            }

            if (!$request->hasFile($key)) continue;

            if ($existing) Storage::disk('public')->delete($existing);

            $path = $this->processImage(
                $request->file($key),
                1200, // slider width
                80    // slider quality
            );

            Setting::setValue($key, $path);
        }

        $this->compactSliders();
        Cache::forget('sliders');
    }

    /* ===============================
     * DEPLOYMENT-SAFE IMAGE HANDLER
     * (GD only | PNG fallback if JPEG not supported)
     * =============================== */
    private function processImage($file, int $width, int $quality): string
    {
        // Check GD capabilities
        $gdInfo = gd_info();
        $hasJpegSupport = isset($gdInfo['JPEG Support']) && $gdInfo['JPEG Support'];
        $hasPngSupport = isset($gdInfo['PNG Support']) && $gdInfo['PNG Support'];

        if (!$hasPngSupport) {
            throw new \RuntimeException('GD library does not support PNG format. Please install PNG support for GD.');
        }

        // Detect the input file type
        $mimeType = $file->getMimeType();
        $isJpeg = in_array($mimeType, ['image/jpeg', 'image/jpg']);

        // If input is JPEG but JPEG support is not available, we cannot process it
        if ($isJpeg && !$hasJpegSupport) {
            throw new \RuntimeException(
                'Cannot process JPEG image. GD library does not have JPEG support. ' .
                'Please convert your image to PNG format before uploading, or install JPEG support for GD. ' .
                'On Windows, you may need to enable JPEG support in php.ini or install a PHP build with JPEG support compiled in.'
            );
        }

        // Determine output format: use JPEG if supported, otherwise PNG
        $ext = $hasJpegSupport ? 'jpg' : 'png';
        $filename = uniqid() . '.' . $ext;
        $path = 'settings/' . $filename;

        try {
            // Use read() instead of make() in v3
            $image = $this->image
                ->read($file->getRealPath())
                ->resize($width, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode($ext, $quality);

            Storage::disk('public')->put($path, (string) $image);
        } catch (\Exception $e) {
            // If reading fails (e.g., trying to read JPEG without support), provide helpful error
            if (strpos($e->getMessage(), 'imagecreatefromjpeg') !== false || $isJpeg) {
                throw new \RuntimeException(
                    'Cannot process JPEG image. GD library does not have JPEG support. ' .
                    'Please convert your image to PNG format before uploading, or install JPEG support for GD. ' .
                    'Original error: ' . $e->getMessage()
                );
            }

            // For other errors, rethrow as-is
            throw $e;
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
