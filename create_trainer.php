<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('email', 'doni.trainer@test.local')->first();
if (!$user) {
    $user = User::create([
        'name' => 'Doni Trainer',
        'email' => 'doni.trainer@test.local',
        'phone' => '08123',
        'password' => bcrypt('secret'),
        'role' => 'staff',
    ]);
    $user->assignRole('User:Trainer');
    echo "✓ Trainer account created: {$user->name}\n";
    echo "✓ Email: {$user->email}\n";
    echo "✓ Role: " . $user->getRoleNames()->first() . "\n";
} else {
    echo "✓ Trainer account already exists: {$user->name}\n";
    echo "✓ Role: " . $user->getRoleNames()->first() . "\n";
}
