<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Menu;

// 1. Give product_transaction permission to member
$role = Role::findByName('User:Member');
$permissionName = 'product_transaction';
Permission::firstOrCreate(['name' => $permissionName]);

if (!$role->hasPermissionTo($permissionName)) {
    $role->givePermissionTo($permissionName);
    echo "Granted product_transaction to Member.\n";
}

// 2. Rename menus
$menusToRename = [
    ['url' => 'member_transaction', 'newName' => 'Riwayat Membership'],
    ['url' => 'service_transaction', 'newName' => 'Riwayat Layanan'],
    ['url' => 'product_transaction', 'newName' => 'Riwayat Produk'],
];

foreach ($menusToRename as $item) {
    $menu = Menu::where('url', $item['url'])->first();
    if ($menu) {
        $oldName = $menu->name;
        $menu->update(['name' => $item['newName']]);
        echo "Renamed menu '$oldName' to '{$item['newName']}'.\n";
    }
}

echo "Database updates complete.\n";
