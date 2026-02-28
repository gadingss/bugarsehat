<?php

namespace App\Http\Controllers;

use App\Repository\HomeMemberRepository;
use App\Repository\HomeOwnerRepository;
use App\Repository\HomeStaffRepository;
use App\Repository\HomeSuperAdminRepository;
use App\Repository\MenuRepository;
use App\Traits\IconComponent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    use IconComponent;
    public function index(Request $request)
    {
        // $user=Auth()->user();
        $config = [
            'title' => 'Dashboards',
            'title-alias' => $this->generateList('dashboard', 'me-2') . ' Dashboards',
            'menu' => MenuRepository::generate($request),
        ];
        $start = Carbon::parse(ENV('APP_START_YEAR', '2017-01-01'))
            ->yearsUntil(Carbon::now()->format('Y-m-d'));
        $start = Collect($start)->transform(function ($res) {
            return $res->format('Y');
        });
        $config['tahun'] = $start->sortDesc()->toArray();
        $user = Auth::user();
        if ($user->hasRole('Super:Admin')) {
            $data = HomeSuperAdminRepository::generate();
            return view('home.index', compact('config', 'data'));
        } elseif ($user->hasRole('User:Owner')) {
            $data = HomeOwnerRepository::generate();
            return view('home.index_owner', compact('config', 'data'));
        } elseif ($user->hasRole('User:Staff')) {
            $data = HomeStaffRepository::generate();
            return view('home.index_staff', compact('config', 'data'));
        } elseif ($user->hasRole('User:Trainer')) {
            // trainers have their own dashboard route, so redirect there
            return redirect()->route('trainer.dashboard');
        } else {
            $data = HomeMemberRepository::generate();
            return view('home.index_member', compact('config', 'data'));
        }
    }

}
