<?php


use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;


if(!function_exists('getSliders')){
  /**
     * Get all sliders in order, cached forever
     *
     * @return array
     */

    function getSliders(): array{

        return Cache::rememberForever('sliders',function(){
            return Setting::query()
            ->where('key','like','slider%')
            ->OrderByRaw("CAST(SUBSTRING(`key`, 7) AS UNSIGNED)")
            ->pluck('value','key')
            ->toArray();
        });

    }

}


if (!function_exists('setting')) {
    /**
     * Get any setting by key, cached forever
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        $settings = Cache::rememberForever('settings', function () {
            return Setting::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }
}
if (!function_exists('getLogo')) {
    /**
     * Get the site logo path
     *
     * @param string|null $default
     * @return string|null
     */
    function getLogo(?string $default = null): ?string
    {
        $logo = setting('logo_image', $default);
        return $logo ? asset('storage/' . $logo) : $default;
    }
}