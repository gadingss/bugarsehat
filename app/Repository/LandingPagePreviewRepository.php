<?php
namespace App\Repository;

use App\Models\LandingPage;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LandingPagePreviewRepository{
    public static function generate($request=[]){
        $result=LandingPage::select(['title','photo_path','file_path','desc'])->get();
        $return=Collect($result)->transform(function($res){
            return $res;
        })->toArray();
        return $return;
    }
}