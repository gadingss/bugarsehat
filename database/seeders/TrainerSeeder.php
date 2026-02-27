<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TrainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define menus
        $menus = [
            [
                'name' => 'Dashboard',
                'url' => 'trainer.dashboard',
                'icon' => 'dashboard',
                'type' => 'menu',
                'order' => 1
            ],
            [
                'name' => 'Jadwal Latihan',
                'url' => 'trainer.schedule.index',
                'icon' => 'calendar',
                'type' => 'menu',
                'order' => 2
            ],
            [
                'name' => 'Progress Latihan',
                'url' => 'trainer.progress.index',
                'icon' => 'trending-up',
                'type' => 'menu',
                'order' => 3
            ],
            [
                'name' => 'Daftar Member',
                'url' => 'trainer.members.index',
                'icon' => 'users',
                'type' => 'menu',
                'order' => 4
            ],
            [
                'name' => 'Ketersediaan Waktu',
                'url' => 'trainer.availability.index',
                'icon' => 'clock',
                'type' => 'menu',
                'order' => 5
            ]
        ];

        // Define permissions based on routes because MenuRepository uses url as permission name
        $permissions = [
            'trainer.dashboard',
            'trainer.schedule.index',
            'trainer.progress.index',
            'trainer.members.index',
            'trainer.availability.index'
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // Insert menus
        foreach ($menus as $menuData) {
            \App\Models\Menu::firstOrCreate(
                ['url' => $menuData['url']],
                $menuData
            );
        }

        // Create or get Trainer role
        $trainerRole = Role::firstOrCreate(['name' => 'User:Trainer', 'guard_name' => 'web']);

        // Assign permissions to role
        $trainerRole->syncPermissions($permissions);

        // Create a user with trainer role
        $user = User::firstOrCreate(
            ['email' => 'trainer@bugarsehat.com'],
            [
                'name' => 'Joko Trainer',
                'password' => Hash::make('password123'),
                'phone' => '081234567890',
                'role' => 'trainer', // Must be enum value: member, staff, owner, admin, trainer
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole($trainerRole);

        $this->command->info('Trainer user created: trainer@bugarsehat.com / password123');
    }
}
