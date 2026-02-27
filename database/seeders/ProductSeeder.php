<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'code' => 'PWP001',
                'name' => 'Protein Whey Premium',
                'description' => 'Protein whey berkualitas tinggi untuk membantu pembentukan otot',
                'price' => 350000,
                'category' => 'Suplemen',
                'stock' => 50,
                'status' => 'active',
            ],
            [
                'code' => 'BED002',
                'name' => 'BCAA Energy Drink',
                'description' => 'Minuman energi dengan BCAA untuk recovery otot',
                'price' => 45000,
                'category' => 'Minuman',
                'stock' => 100,
                'status' => 'active',
            ],
            [
                'code' => 'GTP003',
                'name' => 'Gym Towel Premium',
                'description' => 'Handuk gym berkualitas tinggi, mudah menyerap keringat',
                'price' => 75000,
                'category' => 'Aksesoris',
                'stock' => 30,
                'status' => 'active',
            ],
            [
                'code' => 'SB004',
                'name' => 'Shaker Bottle',
                'description' => 'Botol shaker untuk protein dan suplemen',
                'price' => 35000,
                'category' => 'Aksesoris',
                'stock' => 75,
                'status' => 'active',
            ],
            [
                'code' => 'PWB005',
                'name' => 'Pre-Workout Booster',
                'description' => 'Suplemen pre-workout untuk meningkatkan energi',
                'price' => 250000,
                'category' => 'Suplemen',
                'stock' => 25,
                'status' => 'active',
            ],
            [
                'code' => 'YMP006',
                'name' => 'Yoga Mat Premium',
                'description' => 'Matras yoga anti-slip berkualitas tinggi',
                'price' => 150000,
                'category' => 'Yoga',
                'stock' => 40,
                'status' => 'active',
            ],
            [
                'code' => 'RBS007',
                'name' => 'Resistance Band Set',
                'description' => 'Set resistance band untuk latihan di rumah',
                'price' => 125000,
                'category' => 'Aksesoris',
                'stock' => 60,
                'status' => 'active',
            ],
            [
                'code' => 'CM008',
                'name' => 'Creatine Monohydrate',
                'description' => 'Suplemen creatine untuk meningkatkan performa',
                'price' => 180000,
                'category' => 'Suplemen',
                'stock' => 35,
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
