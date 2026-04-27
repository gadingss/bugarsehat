<?php

namespace App\Repository;

use App\Models\Menu;
use App\Traits\IconComponent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class MenuRepository
{
    use IconComponent;

    public static function generate($request)
    {
        $result = Menu::select(['id', 'name', 'url', 'type', 'sub_id', 'order', 'icon'])
            ->orderBy('sub_id', 'ASC')
            ->orderBy('order', 'ASC')
            ->get()
            ->keyBy('id')
            ->toArray();

        $user = Auth::user();

        foreach ($result as $key => $item) {
            // Batasi SEMUA menu Laporan agar hanya bisa dilihat oleh Owner (User:Owner)
            // Daftar URL laporan: transaction_report, membership_report, checkin_report, income_report, owner.trainer-salary.index
            $laporanUrls = [
                'transaction_report', 
                'membership_report', 
                'checkin_report', 
                'income_report', 
                'owner.trainer-salary.index'
            ];
            
            if (isset($item['url']) && in_array($item['url'], $laporanUrls)) {
                if (!$user->hasRole('User:Owner')) {
                    unset($result[$key]);
                    continue;
                }
            }

            // Hapus menu history_membership dari daftar (diminta dihapus via prompt)
            if (isset($item['url']) && $item['url'] === 'history_membership') {
                unset($result[$key]);
                continue;
            }

            // Hapus menu role_assignment khusus untuk Owner
            if (isset($item['url']) && $item['url'] === 'role_assignment') {
                if ($user->hasRole('User:Owner')) {
                    unset($result[$key]);
                    continue;
                }
            }

            // Hapus menu monitor-trainer dan availability untuk Trainer dan Member
            if (isset($item['url']) && in_array($item['url'], ['staff.monitor-trainer.index', 'trainer.availability.index'])) {
                if ($user->hasRole('User:Trainer') || $user->hasRole('User:Member')) {
                    unset($result[$key]);
                    continue;
                }
            }
            // =================================================================
            // AKHIR BLOK PERBAIKAN
            // =================================================================

            $result[$key]['class'] = '';

            if (!empty($item['url'])) {
                $routeName = $item['url'];



                $result[$key]['permission'] = $routeName;

                // FIX: Map URL 'pengguna.index' to permission 'pengguna' because Spatie only has 'pengguna'
                if ($routeName === 'pengguna.index') {
                    $result[$key]['permission'] = 'pengguna';
                }

                if ($routeName === 'member.schedule.index') {
                    $result[$key]['permission'] = 'member.schedule.view';
                }

                // Bypass permission mapping for monitor-trainer route
                if ($routeName === 'staff.monitor-trainer.index') {
                    unset($result[$key]['permission']);
                }


                try {
                    if (Route::has($routeName)) {
                        $generatedRoute = route($routeName);
                        $result[$key]['url'] = $generatedRoute;
                        $result[$key]['class'] = (url()->current() == $generatedRoute) ? 'active' : '';
                    } else {
                        $result[$key]['url'] = '#';
                    }
                } catch (\Exception $e) {
                    $result[$key]['url'] = '#';
                }
            } else {
                $result[$key]['url'] = '#';
            }

            if (!empty($item['icon'])) {
                $result[$key]['icon'] = self::MenuList($item['icon']);
            }

            // Cek permission user terhadap URL (Logika ini tetap berjalan)
            if (isset($result[$key]['permission']) && !$user->can($result[$key]['permission'])) {
                unset($result[$key]);
                continue;
            }

            // Submenu nesting
            if (in_array($item['sub_id'], array_keys($result))) {
                if (array_key_exists($key, $result)) {
                    $result[$item['sub_id']]['sub'][] = $result[$key];
                    unset($result[$key]);
                }
            }
        }

        // Hapus submenu yang tidak punya sub-menu child
        $result = collect($result)->filter(function ($res) {
            if ($res['type'] === 'submenu' && !array_key_exists('sub', $res)) {
                return false;
            }
            return true;
        });

        // Hapus label terakhir jika tidak ada menu setelahnya
        $last = $result->last();
        if ($last && $last['type'] === 'label') {
            $result->pop();
        }

        return $result->toArray();
    }

    /**
     * Ambil menu berdasarkan nama route/url.
     */
    public function getByRoute($routeName)
    {
        return Menu::where('url', $routeName)->first();
    }
}
