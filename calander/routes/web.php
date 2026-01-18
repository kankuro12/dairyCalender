<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;




/**
 * Language Switcher
 * Allows users to switch between English and Nepali languages
 */
Route::get('/lang/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'np'])) {
        abort(400);
    }
    session()->put('locale', $locale);
    return redirect()->back();
})->name('lang.switch');

/**
 * Public Calendar Routes
 * Display calendar for public users
 */
Route::prefix('calendar')->name('calendar.')->group(function () {
    // Calendar view with optional year/month
    Route::get('/{year?}/{month?}', [CalendarController::class, 'index'])
        ->name('index')
        ->where(['year' => '\d{4}', 'month' => '0?[1-9]|1[0-2]']);

    // Get calendar data via AJAX
    Route::get('/data/{year}/{month}', [CalendarController::class, 'getCalendarData'])
        ->name('data');

    // Get specific day details
    Route::get('/days/{bsDate}', [CalendarController::class, 'getDayDetails'])
        ->name('day.details');
});

/**
 * Public News Routes
 * Display news articles for public users
 */
Route::prefix('news')->name('news.')->group(function () {
    // Single news detail page
    Route::get('/{id}', [NewsController::class, 'show'])
        ->name('show');
});



Route::prefix('admin')->name('admin.')->group(function () {
    // Login routes (guest only)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'adminLoginForm'])
            ->name('login.form');

        Route::post('/login/submit', [AdminAuthController::class, 'adminLoginSubmit'])
            ->name('login.submit');
    });
});



Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {


    // Dashboard
    Route::match(['get', 'post'], '/', [CalendarController::class, 'adminIndex'])
        ->name('index');

    // Get statistics by year (AJAX)
    Route::get('/stats/{year}', [CalendarController::class, 'getStatsByYear'])
        ->name('stats.by.year');

    // Load events (AJAX)
    Route::get('/load-event', [CalendarController::class, 'loadEvents'])
        ->name('load.events');


    // Logout
    Route::post('/logout', [AdminAuthController::class, 'adminLogout'])
        ->name('logout');


    // Add/Edit month data
    Route::match(['get', 'post'], '/add-month-data/{month}', [CalendarController::class, 'addMonthData'])
        ->name('month.data.edit');

    // Get month data by year (AJAX)
    Route::get('/months-data/{year}', [CalendarController::class, 'getMonthsDataByYear'])
        ->name('months.data.by.year');

    // Show month data API (AJAX)
    Route::get('/api/month-data/{month}', [CalendarController::class, 'showMonthData'])
        ->name('month.data.show');



    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/logo', [SettingController::class, 'index'])
            ->name('logo');

        Route::post('/logo', [SettingController::class, 'store'])
            ->name('logo.store');
    });



    Route::prefix('announcements')->name('announcements.')->group(function () {
        // List and create
        Route::get('/', [AnnouncementController::class, 'index'])
            ->name('index');

        Route::post('/', [AnnouncementController::class, 'store'])
            ->name('store');

        // Update and delete
        Route::put('/{announcement}', [AnnouncementController::class, 'update'])
            ->name('update');

        Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])
            ->name('destroy');
    });

    Route::prefix('news')->name('news.')->group(function () {
        // List and create
        Route::get('/', [NewsController::class, 'index'])
            ->name('index');

        Route::post('/', [NewsController::class, 'store'])
            ->name('store');

        // Update and delete
        Route::put('/{news}', [NewsController::class, 'update'])
            ->name('update');

        Route::delete('/{news}', [NewsController::class, 'destroy'])
            ->name('destroy');

        // Fetch from API
        Route::post('/fetch-api', [NewsController::class, 'fetchFromApi'])
            ->name('fetch-api');
    });
});

