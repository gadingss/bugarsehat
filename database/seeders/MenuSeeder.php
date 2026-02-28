<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $menus = [
            ['id' => 1, 'name' => 'Dashboard', 'type' => 'menu', 'order' => 1, 'url' => 'home', 'is_permission' => true, 'icon' => 'dashboard'],

            ['id' => 2, 'name' => 'Manajemen User', 'type' => 'submenu', 'order' => 2, 'is_permission' => true, 'icon' => 'book-reference'],
            ['id' => 3, 'name' => 'Daftar User', 'type' => 'menu', 'order' => 1, 'sub_id' => 2, 'url' => 'pengguna'],
            ['id' => 4, 'name' => 'Role Assignment', 'type' => 'menu', 'order' => 2, 'sub_id' => 2, 'url' => 'role_assignment'],

            ['id' => 5, 'name' => 'Membership', 'type' => 'submenu', 'order' => 3, 'is_permission' => true, 'icon' => 'layer'],
            ['id' => 6, 'name' => 'Daftar Member', 'type' => 'menu', 'order' => 1, 'sub_id' => 5, 'url' => 'user_membership'],
            ['id' => 7, 'name' => 'Paket Membership', 'type' => 'menu', 'order' => 2, 'sub_id' => 5, 'url' => 'packet_membership'],
            ['id' => 8, 'name' => 'Aktivasi/Perpanjang', 'type' => 'menu', 'order' => 3, 'sub_id' => 5, 'url' => 'activation_order'],
            ['id' => 9, 'name' => 'Riwayat Membership', 'type' => 'menu', 'order' => 4, 'sub_id' => 5, 'url' => 'history_membership'],
            ['id' => 10, 'name' => 'Kelola Membership', 'type' => 'menu', 'order' => 5, 'sub_id' => 5, 'url' => 'membership.index'],

            ['id' => 11, 'name' => 'Produk & Layanan', 'type' => 'submenu', 'order' => 4, 'is_permission' => true, 'icon' => 'map'],
            ['id' => 12, 'name' => 'Daftar Produk', 'type' => 'menu', 'order' => 1, 'sub_id' => 11, 'url' => 'product'],
            ['id' => 13, 'name' => 'Daftar Layanan', 'type' => 'menu', 'order' => 2, 'sub_id' => 11, 'url' => 'service'],
            ['id' => 14, 'name' => 'Katalog Produk', 'type' => 'menu', 'order' => 3, 'sub_id' => 11, 'url' => 'products.index'],
            ['id' => 15, 'name' => 'Layanan Member', 'type' => 'menu', 'order' => 4, 'sub_id' => 11, 'url' => 'services.index'],
            ['id' => 16, 'name' => 'Produk Saya', 'type' => 'menu', 'order' => 5, 'sub_id' => 11, 'url' => 'products.my-products'],
            ['id' => 17, 'name' => 'Booking Saya', 'type' => 'menu', 'order' => 6, 'sub_id' => 11, 'url' => 'services.my-bookings'],

            ['id' => 18, 'name' => 'Transaksi', 'type' => 'submenu', 'order' => 5, 'is_permission' => true, 'icon' => 'chart'],
            ['id' => 19, 'name' => 'Produk', 'type' => 'menu', 'order' => 1, 'sub_id' => 18, 'url' => 'product_transaction'],
            ['id' => 20, 'name' => 'Member', 'type' => 'menu', 'order' => 2, 'sub_id' => 18, 'url' => 'member_transaction'],
            ['id' => 21, 'name' => 'Layanan', 'type' => 'menu', 'order' => 3, 'sub_id' => 18, 'url' => 'service_transaction'],

            ['id' => 22, 'name' => 'Laporan', 'type' => 'submenu', 'order' => 6, 'is_permission' => true, 'icon' => 'chart'],
            ['id' => 23, 'name' => 'Laporan Transaksi', 'type' => 'menu', 'order' => 1, 'sub_id' => 22, 'url' => 'transaction_report'],
            ['id' => 24, 'name' => 'Laporan Membership', 'type' => 'menu', 'order' => 2, 'sub_id' => 22, 'url' => 'membership_report'],
            ['id' => 25, 'name' => 'Laporan Check-in', 'type' => 'menu', 'order' => 3, 'sub_id' => 22, 'url' => 'checkin_report'],
            ['id' => 26, 'name' => 'Laporan Pendapatan', 'type' => 'menu', 'order' => 4, 'sub_id' => 22, 'url' => 'income_report'],

            // Menu Trainer
            ['id' => 41, 'name' => 'Menu Pelatih', 'type' => 'submenu', 'order' => 7, 'is_permission' => true, 'icon' => 'user'],
            ['id' => 42, 'name' => 'Jadwal Latihan', 'type' => 'menu', 'order' => 1, 'sub_id' => 41, 'url' => 'trainer.schedule.index'],
            ['id' => 43, 'name' => 'Progress Latihan', 'type' => 'menu', 'order' => 2, 'sub_id' => 41, 'url' => 'trainer.progress.index'],
            ['id' => 44, 'name' => 'Daftar Member', 'type' => 'menu', 'order' => 3, 'sub_id' => 41, 'url' => 'trainer.members.index'],
            ['id' => 45, 'name' => 'Ketersediaan Waktu', 'type' => 'menu', 'order' => 4, 'sub_id' => 41, 'url' => 'trainer.availability.index'],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(['id' => $menu['id']], $menu);
        }
    }
}
