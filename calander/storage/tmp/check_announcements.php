<?php

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$nowUtc = Illuminate\Support\Carbon::now('UTC');
$nowKtm = Illuminate\Support\Carbon::now('Asia/Kathmandu');

$count = Illuminate\Support\Facades\DB::table('announcements')->count();
$active = Illuminate\Support\Facades\DB::table('announcements')->where('status', 1)->count();
$filtered = Illuminate\Support\Facades\DB::table('announcements')
    ->where('status', 1)
    ->where(function ($q) use ($nowKtm) {
        $q->whereNull('start_at')->orWhere('start_at', '<=', $nowKtm);
    })
    ->where(function ($q) use ($nowKtm) {
        $q->whereNull('end_at')->orWhere('end_at', '>=', $nowKtm);
    })
    ->count();

echo "count={$count}\n";
echo "active={$active}\n";
echo "filtered={$filtered}\n";
echo "now_utc=" . $nowUtc->toDateTimeString() . "\n";
echo "now_kathmandu=" . $nowKtm->toDateTimeString() . "\n";

$rows = Illuminate\Support\Facades\DB::table('announcements')
    ->select('id', 'title', 'status', 'start_at', 'end_at', 'priority', 'type')
    ->orderBy('id')
    ->get();

foreach ($rows as $r) {
    echo "#{$r->id} status={$r->status} type={$r->type} priority={$r->priority} start_at=" . ($r->start_at ?? 'NULL') . " end_at=" . ($r->end_at ?? 'NULL') . " title={$r->title}\n";
}
