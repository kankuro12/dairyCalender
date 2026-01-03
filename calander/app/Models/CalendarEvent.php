<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $fillable = [
        'bs_year',
        'bs_month',
        'bs_day',
        'ad_date',
        'title',
        'tithi',
        'is_holiday',
        'holiday_type',
        'extra_events',
        'notes',
    ];
}
