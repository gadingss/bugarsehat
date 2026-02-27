<?php

namespace Database\Seeders;

use App\Models\MembershipPacket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MembershipPacketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $saveList=[
            [
                'name'=>'Trial',
                'price'=>'0',
                'duration_days'=>'7',
                'max_visits'=>'7',
                'name_label'=>'Coba dulu, gratis!',
                'description'=>'Ideal untuk pemula yang ingin mencoba layanan kami tanpa komitmen.',
                'usage'=>'Pengguna baru, eksplorasi awal.',
                'is_publish'=>true
            ],
            [
                'name'=>'Silver',
                'price'=>'100000',
                'duration_days'=>'14',
                'max_visits'=>'24',
                'name_label'=>'Paket hemat, manfaat optimal',
                'description'=>'Akses lebih intensif dalam waktu singkat.',
                'usage'=>'Orang sibuk yang ingin berlatih intens dalam 2 minggu.',
                'is_publish'=>true
            ],
            [


                'name'=>'Gold',
                'price'=>'170000',
                'duration_days'=>'28',
                'max_visits'=>'55',
                'name_label'=>'Lebih lama, lebih fleksibel',
                'description'=>'Kombinasi antara fleksibilitas dan frekuensi latihan tinggi.',
                'usage'=>'Komitmen jangka menengah untuk rutinitas sehat.',
                'is_publish'=>true
            ],
            [


                'name'=>'Silver A',
                'price'=>'120000',
                'duration_days'=>'20',
                'max_visits'=>'20',
                'description'=>'Paket Silver A'
            ],
            [
                'name'=>'Gold',
                'price'=>'170000',
                'duration_days'=>'28',
                'max_visits'=>'55',
                'name_label'=>'Lebih lama, lebih fleksibel',
                'description'=>'Kombinasi antara fleksibilitas dan frekuensi latihan tinggi.',
                'usage'=>'Komitmen jangka menengah untuk rutinitas sehat.',
                'is_publish'=>true
            ],
            [
                'name'=>'Gold A',
                'price'=>'200000',
                'duration_days'=>'40',
                'max_visits'=>'40',
                'description'=>'Paket Silver A'
            ],
            [


                'name'=>'Gold',
                'price'=>'170000',
                'duration_days'=>'28',
                'max_visits'=>'55',
                'name_label'=>'Lebih lama, lebih fleksibel',
                'description'=>'Kombinasi antara fleksibilitas dan frekuensi latihan tinggi.',
                'usage'=>'Komitmen jangka menengah untuk rutinitas sehat.',
                'is_publish'=>true
            ],
            [

                'name'=>'Platinum',
                'price'=>'250000',
                'duration_days'=>'60',
                'max_visits'=>'150',
                'name_label'=>'Paket paling lengkap dan fleksibel',
                'description'=>'Akses maksimal untuk pengguna yang serius berlatih dan menjaga kebugaran secara konsisten.',
                'usage'=>'Member tetap yang ingin kebebasan penuh.',
                'is_publish'=>true
            ],
        ];

        foreach ($saveList as $packet) {
            MembershipPacket::create($packet);
        }
    }
}