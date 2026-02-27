<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MenuSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            MembershipPacketSeeder::class,
            LandingPageSeeder::class,
            ProductSeeder::class,
            ServiceSeeder::class,

            // MasterSeeder::class,
            // RefKecamatanSeeder::class,
            // RefKelurahanSeeder::class,
            // RefDusunSeeder::class,
            // VersionSeeder::class,
        ]);
    }
}
