<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $saveList=[
            ['Welcome to Bugar Sehat!','1.jpg','landing_page','A Healthy and Active Lifestyle, Start your journey to a stronger body and calmer mind. A better quality of life begins here.'],
            ['Transform Your Body & Mind','2.jpg','landing_page','Where Strength Meets Serenity, Our integrated gym and yoga programs help you reach peak physical performance and mental clarity.'],
            ['Modern & Comfortable Facilities','3.png','landing_page','A Space That Inspires Movement, Enjoy a clean, spacious, and well-equipped environment designed to support your fitness goals.'],
        ];
        foreach($saveList as $save){
            $savelist=[
                'title'=>$save[0],
                'photo_path'=>$save[1],
                'file_path'=>$save[2],
                'desc'=>$save[3],
            ];
            LandingPage::insert($savelist);
        };
    }
}
