<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$menus = \App\Models\Menu::get();
file_put_contents('menus_output.json', json_encode($menus, JSON_PRETTY_PRINT));
