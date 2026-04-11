<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Spatie\Permission\Models\Role;

$role = Role::findByName('User:Staff');
$permissions = $role->permissions->pluck('name')->toArray();
file_put_contents('staff_perms.json', json_encode($permissions, JSON_PRETTY_PRINT));
