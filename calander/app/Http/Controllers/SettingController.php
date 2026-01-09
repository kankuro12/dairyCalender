<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SettingController extends Controller
{
    protected ImageManager $image;

    public function __construct()
    {
        $this->image = new ImageManager(new Driver());
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

            'logo_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'remove_logo_image' => 'nullable|boolean',
        ]);

        /* ---- text settings ---- */
        foreach (['site_name', 'contact_phone', 'contact_email', 'contact_address'] as $key) {
            Setting::setValue($key, $validated[$key] ?? null);
        }

        /* ---- logo color ---- */
        $color = $validated['logo_color'] ?? $validated['logo_color_hex'] ?? null;
        Setting::setValue('logo_color', $color);
        Setting::setValue('logo_color_hex', $color);

        Cache::forget('settings');

            Cache::forget('sliders');

        /* ---- logo image ---- */
        $this->handleLogo($request);

        /* ---- sliders ---- */
        $this->handleSliders($request);

        return redirect()
            ->route('events.logo')
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
            300,     // logo width
            90       // logo quality
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
            $rules[$key] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096';
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
                1200,   // slider width
                80      // slider quality
            );

            Setting::setValue($key, $path);
        }

        $this->compactSliders();
        Cache::forget('sliders');
    }

    /* ===============================
     * IMAGE PROCESSOR (INTERVENTION)
     * =============================== */
    private function processImage($file, int $width, int $quality): string
    {
        $filename = uniqid() . '.webp';
        $path = 'settings/' . $filename;

        $image = $this->image
            ->read($file)
            ->scaleDown($width)
            ->toWebp($quality);

        Storage::disk('public')->put($path, (string) $image);

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
