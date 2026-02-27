<?php
namespace App\Repository;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class HomeSuperAdminRepository{
    public static function generate($request=[]){
        return ['data'];
    }
}