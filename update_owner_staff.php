<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Spatie\Permission\Models\Role;
use App\Models\Menu;

// 1. Revoke membership.index from Owner and Staff
$rolesToUpdate = ['User:Owner', 'User:Staff'];
foreach ($rolesToUpdate as $roleName) {
    if ($role = Role::findByName($roleName)) {
        if ($role->hasPermissionTo('membership.index')) {
            $role->revokePermissionTo('membership.index');
            echo "Revoked membership.index from $roleName.\n";
        }
    }
}

// 2. Rename packet_membership menu
$packetMenu = Menu::where('url', 'packet_membership')->first();
if ($packetMenu) {
    $packetMenu->update(['name' => 'Kelola Membership']);
    echo "Renamed packet_membership menu to Kelola Membership.\n";
}

echo "Database updates complete.\n";
