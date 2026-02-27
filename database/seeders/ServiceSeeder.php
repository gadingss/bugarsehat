<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Personal Training',
                'description' => 'Sesi latihan personal dengan trainer berpengalaman',
                'price' => 150000,
                'duration_minutes' => 60,
                'category' => 'Training',
                'is_active' => true,
                'requires_booking' => true,
                'max_participants' => 1,
            ],
            [
                'name' => 'Group Fitness Class',
                'description' => 'Kelas fitness grup dengan berbagai jenis latihan',
                'price' => 50000,
                'duration_minutes' => 45,
                'category' => 'Group Class',
                'is_active' => true,
                'requires_booking' => true,
                'max_participants' => 15,
            ],
            [
                'name' => 'Yoga Class',
                'description' => 'Kelas yoga untuk relaksasi dan fleksibilitas',
                'price' => 75000,
                'duration_minutes' => 60,
                'category' => 'Yoga',
                'is_active' => true,
                'requires_booking' => true,
                'max_participants' => 12,
            ],
            [
                'name' => 'Zumba Class',
                'description' => 'Kelas zumba yang menyenangkan dan energik',
                'price' => 60000,
                'duration_minutes' => 45,
                'category' => 'Dance',
                'is_active' => true,
                'requires_booking' => true,
                'max_participants' => 20,
            ],
            [
                'name' => 'Body Massage',
                'description' => 'Pijat relaksasi untuk pemulihan otot',
                'price' => 200000,
                'duration_minutes' => 90,
                'category' => 'Wellness',
                'is_active' => true,
                'requires_booking' => true,
                'max_participants' => 1,
            ],
            [
                'name' => 'Sauna Session',
                'description' => 'Sesi sauna untuk detoksifikasi dan relaksasi',
                'price' => 25000,
                'duration_minutes' => 30,
                'category' => 'Wellness',
                'is_active' => true,
                'requires_booking' => false,
                'max_participants' => 8,
            ],
            [
                'name' => 'Nutrition Consultation',
                'description' => 'Konsultasi nutrisi dengan ahli gizi',
                'price' => 100000,
                'duration_minutes' => 45,
                'category' => 'Consultation',
                'is_active' => true,
                'requires_booking' => true,
                'max_participants' => 1,
            ],
            [
                'name' => 'Body Composition Analysis',
                'description' => 'Analisis komposisi tubuh lengkap',
                'price' => 50000,
                'duration_minutes' => 15,
                'category' => 'Assessment',
                'is_active' => true,
                'requires_booking' => false,
                'max_participants' => 1,
            ],
            [
                'name' => 'Swimming Pool Access',
                'description' => 'Akses kolam renang untuk member',
                'price' => 0,
                'duration_minutes' => 120,
                'category' => 'Facility',
                'is_active' => true,
                'requires_booking' => false,
                'max_participants' => 50,
            ],
            [
                'name' => 'Pilates Class',
                'description' => 'Kelas pilates untuk core strength dan postur',
                'price' => 80000,
                'duration_minutes' => 50,
                'category' => 'Pilates',
                'is_active' => true,
                'requires_booking' => true,
                'max_participants' => 10,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
