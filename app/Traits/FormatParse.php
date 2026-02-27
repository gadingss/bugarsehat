<?php
namespace App\Traits;

trait FormatParse{
    public static function parseQuantity($qt=0,$decimal=false){
        $result=number_format($qt,$decimal,',','.');
        $result=str_replace(',00',"",$result);
        return $result;
    }
	public static function money_float($str=0){
        return str_replace('.',"",$str);
    }
    public static function replaceFromDecimal($listInput,$arrayToDecimal){
        foreach($arrayToDecimal as $item){
            if(in_array($item,array_keys($listInput))){
                $listInput[$item]=self::parseQuantity($listInput[$item],2);
            }
        }
        return $listInput;
    }
    public static function replaceToDecimal($listInput,$arrayToDecimal){
        foreach($arrayToDecimal as $item){
            if(in_array($item,array_keys($listInput))){
                if($listInput[$item]){
                    $listInput[$item]=str_replace('.','',$listInput[$item]);
                    $listInput[$item]=str_replace(',','.',$listInput[$item]);
                }else{
                    $listInput[$item]=null;
                }
                
            }
        }
        return $listInput;
    }

    public static function strtobirth($str=0){
        $str=self::niktobirth($str);
        if(!$str){
            return false;
        }
        return join('-',$str);
    }

    public static function strtoage($str=0){
        $str=self::niktobirth($str);
        if(!$str){
            return false;
        }
        return date("Y")-$str['y'];
    }

    public static function niktobirth($str=0){
        if(strlen($str)!=16)
        {
            return false;
        }
        $day=substr($str, 6, 2);
        $dif=40;
        $yearNow=date("Y");
        $year=substr($str, 10, 2);
        if(substr($yearNow, 2, 2)>=substr($str, 10, 2)){
            $year='20'.$year;
        }else{
            $year='19'.$year;
        }
        return [
            'd'=>((int)$day>$dif)?sprintf("%02s", $day-$dif):$day,
            'm'=>substr($str, 8, 2),
            'y'=>$year
        ];
    }
}