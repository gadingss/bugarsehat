<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests,DispatchesJobs, ValidatesRequests;

    public function successResponse($message='success', $data=null,$codeStatus=Response::HTTP_OK){
        $result=['message' => $message,'status' => $codeStatus];
        if($data){
            $result=array_merge($data,$result);
        }
        return response()->json($result, $codeStatus);
    }

    public function successResponseVersion($versionapp,$message='success',$codeStatus=Response::HTTP_OK){
        $result=['message' => $message,'version' => $versionapp,'status' => $codeStatus];
        return response()->json($result, $codeStatus);
    }

    public function errorResponse($message='Failed', $statusCode = 10, $data=null, $codeStatus= Response::HTTP_BAD_REQUEST){
        $info=($data)?json_encode($data):'';
        return response()->json([
            'status' => $statusCode,
            'message' => $message." ".$info,
            'data' => $data,
        ], $codeStatus);
    }
}
