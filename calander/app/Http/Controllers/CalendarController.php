<?php

namespace App\Http\Controllers;
use Pratiksh\Nepalidate\Services\NepaliDate;
use Illuminate\Support\Facades\Http;
use carbon\carbon;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CalendarController extends Controller
{
    public function index($year = null, $month = null){


        $nowAt = \Carbon\Carbon::now('Asia/Kathmandu');

            $announcements = Cache::remember('announcement_bar', 3600, function () use ($nowAt) {
                return DB::table('announcements')
                ->where('status', 1)
                ->where(function ($q) use ($nowAt) {
                $q->whereNull('start_at')
                ->orWhere('start_at', '<=', $nowAt);
                })
                ->where(function ($q) use ($nowAt) {
                $q->whereNull('end_at')
                ->orWhere('end_at', '>=', $nowAt);
        })
        ->orderByDesc('priority')
        ->orderByDesc('start_at')

        ->get();
});

    //    dd($settings);
        // dd($NepaliDate,$now,$detailsnm);
        return view('calendar.layout.app',compact('announcements','year','month'));
    }
    public function adminIndex(Request $request){
        if($request->isMethod('post')){
            return response()->json(['message' => 'POST request received']);
        }

        // Get current Nepali year from JavaScript NepaliFunction (we'll pass it from frontend)
        // For now, get it from the first available event or use a default
        $currentBsYear = CalendarEvent::orderBy('bs_year', 'desc')->value('bs_year') ?? 2082;

        // Get statistics for dashboard - filtered by current BS year
        $totalEvents = CalendarEvent::where('bs_year', $currentBsYear)->count();
        $totalHolidays = CalendarEvent::where('bs_year', $currentBsYear)
            ->where('is_holiday', true)
            ->count();
        $activeAnnouncements = DB::table('announcements')->where('status', true)->count();

        // Get month names in Nepali
        $nepaliMonths = [
            1 => 'बैशाख',
            2 => 'जेष्ठ',
            3 => 'आषाढ',
            4 => 'श्रावण',
            5 => 'भाद्र',
            6 => 'आश्विन',
            7 => 'कार्तिक',
            8 => 'मंसिर',
            9 => 'पौष',
            10 => 'माघ',
            11 => 'फाल्गुन',
            12 => 'चैत्र',
        ];

        // Get event counts per month for current year only
        $eventCounts = CalendarEvent::selectRaw('bs_month, COUNT(*) as count')
            ->where('bs_year', $currentBsYear)
            ->groupBy('bs_month')
            ->pluck('count', 'bs_month')
            ->toArray();

        return view('backend.app', compact(
            'totalEvents',
            'totalHolidays',
            'activeAnnouncements',
            'nepaliMonths',
            'eventCounts',
            'currentBsYear'
        ));
    }
    public function loadEvents(){

        $response = Http::get('http://localhost:3000/api/calendar/summary');
        if($response->failed()){
             return response()->json(['error' => 'Failed to fetch events'], 500);
                    }


                     return $response->json();
    }
    public function addMonthData(Request $request, $month){
        if($request->isMethod('post')){


         foreach($request ->input('data',[]) as $bsDate =>$payload){
            [$bsYear , $bsMonth, $bsDay] = array_map('intval', explode('-', $bsDate));
            CalendarEvent::updateOrCreate(
                [
                    'bs_year'=>$bsYear,
                    'bs_month'=>$bsMonth,
                    'bs_day'=>$bsDay,
                ],
                [
                    'ad_date'=>$payload['ad_date'],
                    'title'=>$payload['event'] ?? '',
                    'tithi'=>$payload['date_text'] ?? null,
                    'is_holiday'=>!empty($payload['holiday']),
                    'holiday_type'=>$payload['holiday_type'] ?? null,
                    'extra_events'=>$payload['extra_event'] ? json_encode($payload['extra_event']) : null,
                    'notes'=>$payload['notes'] ?? null,
                ]
                );
         }
         return back()->with('success','Month data saved successfully.');

        }
        // $response= Http::get('http://localhost:3000/api/calendar/summary');
        // if($response->failed()){
        //      return response()->json(['error' => 'Failed to fetch events'], 500);
        //             }
        // $calendarSummary = collect($response->json('calendarSummary'));
        // $monthData=$calendarSummary->firstWhere('monthIndex', (int)$month);
        // if(!$monthData){
        //     abort(404, 'Month data not found');
        // }
        // $dayByDate=collect($monthData['days'])->mapWithKeys(function($day){
        //  [$y,$m,$d]=explode('-',$day['nepaliDate']);
        //  $normalizedKey= sprintf('%d-%d-%d',$y,(int)$m,(int)$d);
        //  return [$normalizedKey => $day];
        // });
        $daysInMonth =$request->query('days');
        $year=$request->query('year');

        $events = DB::table('calendar_events')->where('bs_year', $year)
            ->where('bs_month', $month)
            ->get()
            ->keyBy(function ($item) {
                return sprintf('%d-%d-%d', $item->bs_year, (int)$item->bs_month, (int)$item->bs_day);
            });

        // Display the form to add data for the specified month
        return view('backend.month_data', ['month' => $month,'events' => $events
        , 'daysInMonth' => $daysInMonth, 'year' => $year]);
    }
    public function showMonthData($month){
        $response=Http::get('http://localhost:3000/api/calendar/summary');
        if($response->failed()){
             return response()->json(['error' => 'Failed to fetch events'], 500);
        }
        $calendarSummary = collect($response->json('calendarSummary'));
        $monthData=$calendarSummary->firstWhere('monthIndex', (int)$month);
        if(!$monthData){
            return response()->json(['error' => 'Month data not found'], 404);
        }
        $dayByDate=collect($monthData['days'])->mapWithKeys(function($day){
         [$y,$m,$d]=explode('-',$day['nepaliDate']);
         $normalizedKey= sprintf('%d-%d-%d',$y,(int)$m,(int)$d);
         return [$normalizedKey => $day];
        });

        return response()->json($dayByDate);
    }
    public function getCalendarData($year, $month){
        $events =DB::table('calendar_events')->where('bs_year', $year)->where('bs_month',$month)->get()->keyBy(function($item){
            return sprintf('%04d-%02d-%02d', $item->bs_year, $item->bs_month, $item->bs_day);
        });
        return response()->json($events);
    }

    public function getMonthsDataByYear($year){
        // Get event counts per month for the selected year
        $eventCounts = CalendarEvent::selectRaw('bs_month, COUNT(*) as count')
            ->where('bs_year', $year)
            ->groupBy('bs_month')
            ->pluck('count', 'bs_month')
            ->toArray();

        return response()->json([
            'year' => $year,
            'eventCounts' => $eventCounts
        ]);
    }

    public function getStatsByYear($year){
        // Get statistics for the specified year
        $totalEvents = CalendarEvent::where('bs_year', $year)->count();
        $totalHolidays = CalendarEvent::where('bs_year', $year)
            ->where('is_holiday', true)
            ->count();

        return response()->json([
            'year' => $year,
            'totalEvents' => $totalEvents,
            'totalHolidays' => $totalHolidays
        ]);
    }
}
