<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $permissionList=[
            ['name' => 'home','menu_id'=>'1','label_name'=>'View'],
            ['name' => 'pengguna','menu_id'=>'3','label_name'=>'View'],
            ['name' => 'pengguna.create','menu_id'=>'3','label_name'=>'Create'],
            ['name' => 'pengguna.update','menu_id'=>'3','label_name'=>'Update'],
            ['name' => 'pengguna.delete','menu_id'=>'3','label_name'=>'Delete'],
            ['name' => 'pengguna.verify','menu_id'=>'3','label_name'=>'Verifikasi'],

            ['name' => 'role_assignment','menu_id'=>'4','label_name'=>'View'],
            ['name' => 'role_assignment.create','menu_id'=>'4','label_name'=>'Create'],
            ['name' => 'role_assignment.update','menu_id'=>'4','label_name'=>'Update'],
            ['name' => 'role_assignment.delete','menu_id'=>'4','label_name'=>'Delete'],

            ['name' => 'packet_membership','menu_id'=>'6','label_name'=>'View'],
            ['name' => 'packet_membership.create','menu_id'=>'6','label_name'=>'Create'],
            ['name' => 'packet_membership.update','menu_id'=>'6','label_name'=>'Update'],
            ['name' => 'packet_membership.delete','menu_id'=>'6','label_name'=>'Delete'],

            ['name' => 'user_membership','menu_id'=>'24','label_name'=>'View'],
            ['name' => 'user_membership.create','menu_id'=>'24','label_name'=>'Create'],
            ['name' => 'user_membership.update','menu_id'=>'24','label_name'=>'Update'],
            ['name' => 'user_membership.delete','menu_id'=>'24','label_name'=>'Delete'],

            ['name' => 'history_membership','menu_id'=>'8','label_name'=>'View'],
            ['name' => 'packet_membership.export','menu_id'=>'8','label_name'=>'Export'],

            ['name' => 'activation_order','menu_id'=>'7','label_name'=>'View'],
            ['name' => 'activation_order.create','menu_id'=>'7','label_name'=>'Create'],
            ['name' => 'activation_order.update','menu_id'=>'7','label_name'=>'Update'],
            ['name' => 'activation_order.delete','menu_id'=>'7','label_name'=>'Delete'],

            ['name' => 'product','menu_id'=>'10','label_name'=>'View'],
            ['name' => 'product.create','menu_id'=>'10','label_name'=>'Create'],
            ['name' => 'product.update','menu_id'=>'10','label_name'=>'Update'],
            ['name' => 'product.delete','menu_id'=>'10','label_name'=>'Delete'],

            ['name' => 'service','menu_id'=>'11','label_name'=>'View'],
            ['name' => 'service.create','menu_id'=>'11','label_name'=>'Create'],
            ['name' => 'service.update','menu_id'=>'11','label_name'=>'Update'],
            ['name' => 'service.delete','menu_id'=>'11','label_name'=>'Delete'],

            ['name' => 'product_transaction','menu_id'=>'13','label_name'=>'View'],
            ['name' => 'product_transaction.create','menu_id'=>'13','label_name'=>'Create'],
            ['name' => 'product_transaction.update','menu_id'=>'13','label_name'=>'Update'],
            ['name' => 'product_transaction.delete','menu_id'=>'13','label_name'=>'Delete'],
            ['name' => 'product_transaction.export','menu_id'=>'13','label_name'=>'Export'],

            ['name' => 'member_transaction','menu_id'=>'14','label_name'=>'View'],
            ['name' => 'member_transaction.create','menu_id'=>'14','label_name'=>'Create'],
            ['name' => 'member_transaction.update','menu_id'=>'14','label_name'=>'Update'],
            ['name' => 'member_transaction.delete','menu_id'=>'14','label_name'=>'Delete'],
            ['name' => 'member_transaction.export','menu_id'=>'14','label_name'=>'Export'],

            // Permissions untuk menu member baru
            ['name' => 'membership.index','menu_id'=>'25','label_name'=>'View'],
            ['name' => 'products.index','menu_id'=>'26','label_name'=>'View'],
            ['name' => 'services.index','menu_id'=>'27','label_name'=>'View'],
            ['name' => 'products.my-products','menu_id'=>'28','label_name'=>'View'],
            ['name' => 'services.my-bookings','menu_id'=>'29','label_name'=>'View'],
            
            ['name' => 'checkin.index','menu_id'=>'31','label_name'=>'View'],
            ['name' => 'checkin.qr-scan','menu_id'=>'32','label_name'=>'View'],
            ['name' => 'checkin.history','menu_id'=>'33','label_name'=>'View'],
            
            ['name' => 'profile.index','menu_id'=>'35','label_name'=>'View'],
            ['name' => 'profile.edit','menu_id'=>'36','label_name'=>'View'],
            ['name' => 'profile.membership-status','menu_id'=>'37','label_name'=>'View'],

            ['name' => 'service_transaction','menu_id'=>'15','label_name'=>'View'],
            ['name' => 'service_transaction.create','menu_id'=>'15','label_name'=>'Create'],
            ['name' => 'service_transaction.update','menu_id'=>'15','label_name'=>'Update'],
            ['name' => 'service_transaction.delete','menu_id'=>'15','label_name'=>'Delete'],
            ['name' => 'service_transaction.export','menu_id'=>'15','label_name'=>'Export'],

            ['name' => 'landing_page','menu_id'=>'21','label_name'=>'View'],
            // ['name' => 'landing_page.create','menu_id'=>'21','label_name'=>'Create'],
            // ['name' => 'landing_page.update','menu_id'=>'21','label_name'=>'Update'],
            // ['name' => 'landing_page.delete','menu_id'=>'21','label_name'=>'Delete'],

            ['name' => 'transaction_report','menu_id'=>'17','label_name'=>'View'],
            ['name' => 'transaction_report.export','menu_id'=>'17','label_name'=>'Export'],
            ['name' => 'membership_report','menu_id'=>'18','label_name'=>'View'],
            ['name' => 'membership_report.export','menu_id'=>'18','label_name'=>'Export'],
            ['name' => 'checkin_report','menu_id'=>'19','label_name'=>'View'],
            ['name' => 'checkin_report.export','menu_id'=>'19','label_name'=>'Export'],
            ['name' => 'income_report','menu_id'=>'20','label_name'=>'View'],
            ['name' => 'income_report.export','menu_id'=>'20','label_name'=>'Export'],

            ['name' => 'configuration_payment','menu_id'=>'23','label_name'=>'View'],
            ['name' => 'configuration_payment.create','menu_id'=>'23','label_name'=>'Create'],
            ['name' => 'configuration_payment.update','menu_id'=>'23','label_name'=>'Update'],
            ['name' => 'configuration_payment.delete','menu_id'=>'23','label_name'=>'Delete'],

            ['name' => 'landing_page.berita','menu_id'=>'38','label_name'=>'View'],
            ['name' => 'landing_page.berita.create','menu_id'=>'38','label_name'=>'Create'],
            ['name' => 'landing_page.berita.update','menu_id'=>'38','label_name'=>'Update'],
            ['name' => 'landing_page.berita.delete','menu_id'=>'38','label_name'=>'Delete'],
            ['name' => 'landing_page.gallery','menu_id'=>'39','label_name'=>'View'],
            ['name' => 'landing_page.gallery.create','menu_id'=>'39','label_name'=>'Create'],
            ['name' => 'landing_page.gallery.update','menu_id'=>'39','label_name'=>'Update'],
            ['name' => 'landing_page.gallery.delete','menu_id'=>'39','label_name'=>'Delete'],
            ['name' => 'landing_page.promo','menu_id'=>'40','label_name'=>'View'],
            ['name' => 'landing_page.promo.create','menu_id'=>'40','label_name'=>'Create'],
            ['name' => 'landing_page.promo.update','menu_id'=>'40','label_name'=>'Update'],
            ['name' => 'landing_page.promo.delete','menu_id'=>'40','label_name'=>'Delete'],

            ['name' => 'profile','label_name'=>'View'],
            ['name' => 'profile.create','label_name'=>'Create'],
            ['name' => 'profile.update','label_name'=>'Update'],
            ['name' => 'profile.delete','label_name'=>'Delete'],

            // Trainer and Booking Permissions
            ['name' => 'trainer.dashboard.view', 'menu_id' => null, 'label_name' => 'View Trainer Dashboard'],
            ['name' => 'trainer.schedule.view', 'menu_id' => null, 'label_name' => 'View Schedules'],
            ['name' => 'trainer.schedule.create', 'menu_id' => null, 'label_name' => 'Create Schedules'],
            ['name' => 'trainer.schedule.update', 'menu_id' => null, 'label_name' => 'Update Schedules'],
            ['name' => 'trainer.schedule.delete', 'menu_id' => null, 'label_name' => 'Delete Schedules'],
            ['name' => 'member.schedule.view', 'menu_id' => null, 'label_name' => 'View Member Schedules'],
            ['name' => 'member.booking.create', 'menu_id' => null, 'label_name' => 'Create Bookings'],
            
        ];
        $allMenu=[];
        foreach($permissionList as $permission){
            $allMenu[]=$permission['name'];
            $permission['guard_name']="web";
            Permission::insertOrIgnore($permission,$permission);
            $permission['guard_name']="api";
            Permission::insertOrIgnore($permission,$permission);
        }
        $fullThrotleSAWeb=Role::firstOrCreate(['name' => 'Super:Admin','guard_name'=>'web']);
        $fullThrotleSAApi=Role::firstOrCreate(['name' => 'Super:Admin','guard_name'=>'api']);
        $ownerWeb=Role::firstOrCreate(['name' => 'User:Owner','guard_name'=>'web']);
        $ownerApi=Role::firstOrCreate(['name' => 'User:Owner','guard_name'=>'api']);
        $staffWeb=Role::firstOrCreate(['name' => 'User:Staff','guard_name'=>'web']);
        $staffApi=Role::firstOrCreate(['name' => 'User:Staff','guard_name'=>'api']);
        $memberWeb=Role::firstOrCreate(['name' => 'User:Member','guard_name'=>'web']);
        $memberApi=Role::firstOrCreate(['name' => 'User:Member','guard_name'=>'api']);
        $trainerWeb = Role::firstOrCreate(['name' => 'User:Trainer', 'guard_name' => 'web']);
        $trainerApi = Role::firstOrCreate(['name' => 'User:Trainer', 'guard_name' => 'api']);
        $staf=[
            'home',
            // 'pengguna',
            // 'pengguna.create',
            // 'pengguna.update',
            // 'pengguna.delete',
            // 'pengguna.verify',
            // 'role_assignment',
            // 'role_assignment.create',
            // 'role_assignment.update',
            'packet_membership',
            'packet_membership.create',
            'packet_membership.update',
            'user_membership',
            'user_membership.create',
            'user_membership.update',
            'history_membership',
            'packet_membership.export',
            'activation_order',
            'activation_order.create',
            'activation_order.update',
            'activation_order.delete',
            'product',
            'product.create',
            'product.update',
            'service',
            'service.create',
            'service.update',
            'product_transaction',
            'product_transaction.create',
            'product_transaction.update',
            'product_transaction.export',
            'member_transaction',
            'member_transaction.create',
            'member_transaction.update',
            'member_transaction.export',
            'service_transaction',
            'service_transaction.create',
            'service_transaction.update',
            'service_transaction.delete',
            'service_transaction.export',
            // 'landing_page',
            'landing_page.berita',
            'landing_page.berita.create',
            'landing_page.berita.update',
            'landing_page.berita.delete',
            'landing_page.gallery',
            'landing_page.gallery.create',
            'landing_page.gallery.update',
            'landing_page.gallery.delete',
            'landing_page.promo',
            'landing_page.promo.create',
            'landing_page.promo.update',
            'landing_page.promo.delete',
            'transaction_report',
            'transaction_report.export',
            'membership_report',
            'membership_report.export',
            'checkin_report',
            'checkin_report.export',
            'income_report',
            'income_report.export',
            'profile',
            'profile.create',
            'profile.update',
            'profile.delete',

        ];
        $member=[
            'home',
            'packet_membership',
            'history_membership',
            'packet_membership.export',
            'activation_order',
            'product',
            'service',
            'member_transaction',
            'member_transaction.export',
            'service_transaction',
            'service_transaction.export', 
            'profile',
            'profile.create',
            'profile.update',
            'profile.delete',
            'member.schedule.view',
            'member.booking.create',
        ];
        $trainer = [
            'home',
            'trainer.dashboard.view',
            'trainer.schedule.view',
            'trainer.schedule.create',
            'trainer.schedule.update',
            'trainer.schedule.delete',
            'profile',
            'profile.update',
        ];
        $ownerWeb->syncPermissions($allMenu);
        $ownerApi->syncPermissions($allMenu);
        $staffWeb->syncPermissions($staf);
        $staffApi->syncPermissions($staf);
        $memberWeb->syncPermissions($member);
        $memberApi->syncPermissions($member);
        $trainerWeb->syncPermissions($trainer);
        $trainerApi->syncPermissions($trainer);

    }
}
