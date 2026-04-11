<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Menu;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddTrainerSalaryMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-trainer-salary-menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add trainer salary menu and assign permission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create permission if it doesn't exist
        $permission = Permission::firstOrCreate(['name' => 'salary.index']);
        
        // Also add full route permission if needed
        $permission2 = Permission::firstOrCreate(['name' => 'trainer.salary.index']);

        // Assign permission to Trainer role
        $role = Role::findByName('User:Trainer');
        if ($role) {
            $role->givePermissionTo($permission);
            $role->givePermissionTo($permission2);
            $this->info("Permissions given to Trainer role.");
        }

        // Create menu if it doesn't exist
        $menuUrl = 'trainer.salary.index';
        $menu = Menu::where('url', $menuUrl)->first();
        
        if (!$menu) {
            // Find a parent menu for Trainer, or just place it in the root
            $parent = Menu::where('name', 'Menu Utama')->orWhere('name', 'Main Menu')->whereNull('sub_id')->first();
            
            Menu::create([
                'name' => 'Penggajian',
                'url' => $menuUrl,
                'type' => 'menu',
                'icon' => 'money', // or any other class used
                'order' => 10,
                'sub_id' => $parent ? $parent->id : null
            ]);
            $this->info("Menu 'Penggajian' for Trainer created.");
        } else {
            $this->info("Menu 'Penggajian' already exists.");
        }
    }
}
