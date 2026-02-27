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
            // =================================================================
            // BLOK PERBAIKAN: Pengecualian Khusus untuk Laporan Pendapatan
            // =================================================================
            // Kita tambahkan pengecekan khusus di sini. Jika nama route (dari kolom 'url')
            // adalah untuk laporan pendapatan, kita pastikan hanya role 'owner' yang bisa melihatnya.
            // Ganti 'income.report.index' jika nama route Anda berbeda.
            // Pengecekan $user->hasRole('owner') mengasumsikan Anda menggunakan Spatie Permission.
            if (isset($item['url']) && $item['url'] === 'income.report.index' && !$user->hasRole('owner')) {
                unset($result[$key]); // Hapus menu ini dari daftar
                continue; // Lanjut ke item menu berikutnya
            }
            // =================================================================
            // AKHIR BLOK PERBAIKAN
            // =================================================================

            $result[$key]['class'] = '';

            if (!empty($item['url'])) {
                $routeName = $item['url'];
                $result[$key]['permission'] = $routeName;

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
