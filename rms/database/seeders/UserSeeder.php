<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = new User();
            $admin->first_name = 'Super';
            $admin->last_name = 'Admin';
            $admin->name = 'Super Admin';
            $admin->contact = '1234567890';
            $admin->email = 'admin@gmail.com';
            $admin->password = Hash::make('12345678');
            $admin->status = 1;
            $admin->save();

            // $profile = new UserProfile();
            // $profile->user_id = $admin->id;
            // $profile->profile = 'user/user.png';
            // $profile->save();

            //assign role
            $admin->assignRole('super_admin');

            //Give Permission
            // $admin->syncPermissions($permissions);
    }
}
