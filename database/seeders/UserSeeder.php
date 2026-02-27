<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now=now();
        $password=Hash::make('@p4sw0rd_');
        $passwordAdmin=Hash::make('password');
        $saveList=[
            [
                'name'=>'Super-Admin',
                'email'=>'super@gmail.com',
                'email_verified_at'=>$now,
                'password'=>$passwordAdmin,
                'created_at'=>$now,
                'role'=>'admin'
            ],
            [
                'name'=>'Owner',
                'email'=>'owner@gmail.com',
                'email_verified_at'=>$now,
                'password'=>$password,
                'created_at'=>$now,
                'role'=>'owner'
            ],
            [
                'name'=>'Staff 1',
                'email'=>'staff1@gmail.com',
                'email_verified_at'=>$now,
                'password'=>$password,
                'created_at'=>$now,
                'role'=>'staff'
            ],
            [
                'name'=>'Staff 2',
                'email'=>'staff2@gmail.com',
                'email_verified_at'=>$now,
                'password'=>$password,
                'created_at'=>$now,
                'role'=>'staff'
            ],
            [
                'name'=>'Member 1',
                'email'=>'member1@gmail.com',
                'email_verified_at'=>$now,
                'password'=>$password,
                'created_at'=>$now,
                'role'=>'member'
            ],
            [
                'name'=>'Member 2',
                'email'=>'member2@gmail.com',
                'email_verified_at'=>$now,
                'password'=>$password,
                'created_at'=>$now,
                'role'=>'member'
            ],
            [
                'name'=>'Member 3',
                'email'=>'member3@gmail.com',
                'email_verified_at'=>$now,
                'password'=>$password,
                'created_at'=>$now,
                'role'=>'member'
            ],
        ];
        $spAdmin=Role::where(['name'=>'Super:Admin','guard_name'=>'web'])->first();
        $spAdminApi=Role::where(['name'=>'Super:Admin','guard_name'=>'api'])->first();
        $owner=Role::where(['name'=>'User:Owner','guard_name'=>'web'])->first();
        $ownerApi=Role::where(['name'=>'User:Owner','guard_name'=>'api'])->first();
        $staff=Role::where(['name'=>'User:Staff','guard_name'=>'web'])->first();
        $staffApi=Role::where(['name'=>'User:Staff','guard_name'=>'api'])->first();
        $member=Role::where(['name'=>'User:Member','guard_name'=>'web'])->first();
        $memberApi=Role::where(['name'=>'User:Member','guard_name'=>'api'])->first();
        foreach($saveList as $save){
            $user=User::firstOrCreate(['name'=>$save['name'],'email'=>$save['email']],$save);
            if($save['role']=='admin'){
                $user->assignRole($spAdmin);
                $user->assignRole($spAdminApi);
            }elseif($save['role']=='owner'){
                $user->assignRole($owner);
                $user->assignRole($ownerApi);
            }elseif($save['role']=='staff'){
                $user->assignRole($staff);
                $user->assignRole($staffApi);
            }else{
                $user->assignRole($member);
                $user->assignRole($memberApi);
            }
        }
    }
    
}
