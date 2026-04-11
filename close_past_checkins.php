<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logs = App\Models\CheckinLog::whereNull('checkout_time')
    ->whereDate('checkin_time', '<', now()->toDateString())
    ->get();

foreach($logs as $log) {
    if (!$log->checkin_time) continue;
    $log->update([
        'checkout_time' => $log->checkin_time->copy()->setTime(23, 59, 59),
        'notes' => ($log->notes ? $log->notes . ' | ' : '') . 'Auto checkout by system'
    ]);
    echo 'Closed ' . $log->id . PHP_EOL;
}
echo 'Done';
