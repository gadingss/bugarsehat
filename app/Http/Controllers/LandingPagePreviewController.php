<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Repository\LandingPagePreviewRepository;
use App\Repository\MembershipPacketRepository;
use Illuminate\Http\Request;

class LandingPagePreviewController extends Controller
{
    public function index(Request $request){
        $config=['title'=>'Landing Page'];
        $data['slide']=LandingPagePreviewRepository::generate();
        $data['membership_packet']=MembershipPacketRepository::getactivelandingpage();
        // dd($data['membership_packet']);
        
        return view('layouts.app-landing',compact('config','data'));
    }
}
