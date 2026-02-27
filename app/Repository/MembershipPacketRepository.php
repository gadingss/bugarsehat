<?php
namespace App\Repository;

use App\Models\MembershipPacket;
use App\Traits\FormatParse;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MembershipPacketRepository{
    use FormatParse;
    public static function generate($request=[]){
        
    }
    public static function getactivelandingpage($request=[]){
        $result=MembershipPacket::select(['name','price','duration_days','max_visits','description','name_label','usage'])->where(['is_publish'=>true])->get();
        $return=Collect($result)->transform(function($res){
            $res['price']=self::parseQuantity($res['price']);
            return $res;
        })->toArray();
        return $return;
    }
}