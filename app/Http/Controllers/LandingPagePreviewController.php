<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\Product;
use App\Models\Service;
use App\Repository\LandingPagePreviewRepository;
use App\Repository\MembershipPacketRepository;
use Illuminate\Http\Request;

class LandingPagePreviewController extends Controller
{
    public function index(Request $request)
    {
        $config = ['title' => 'Landing Page'];
        $data['slide'] = LandingPagePreviewRepository::generate();
        $data['membership_packet'] = MembershipPacketRepository::getactivelandingpage();

        // Fetch Top 3 Active Products & Services 
        $data['products'] = Product::where('is_active', true)->limit(3)->get();
        $data['services'] = Service::where('is_active', true)->limit(3)->get();

        return view('layouts.app-landing', compact('config', 'data'));
    }
}
