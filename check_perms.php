<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Spatie\Permission\Models\Role;

$role = Role::findByName('User:Member');
$permissions = $role->permissions->pluck('name')->toArray();

$ownerRole = Role::findByName('User:Owner');
$ownerPerms = $ownerRole->permissions->pluck('name')->toArray();

file_put_contents('perms_output2.json', json_encode(['member' => $permissions, 'owner' => $ownerPerms], JSON_PRETTY_PRINT));
