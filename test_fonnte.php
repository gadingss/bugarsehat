<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\User;
use App\Services\FonnteService;

$user = User::where('name', 'memberbaru')->first();
if (!$user) {
    echo "User memberbaru not found.\n";
    exit;
}

$user->phone = '6285736508439';
$user->save();
echo "Updated phone number for {$user->name} to {$user->phone}\n";

$message = "Halo {$user->name}, ini adalah pesan notifikasi testing dari sistem Bugar Sehat Gym & Yoga mengggunakan Fonnte.";
echo "Sending test message...\n";

$result = FonnteService::sendMessage($user->phone, $message);

if ($result) {
    echo "Message sent successfully!\n";
} else {
    echo "Failed to send message. Please check your storage/logs/laravel.log or .env FONNTE_TOKEN.\n";
}
