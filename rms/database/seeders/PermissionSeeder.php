<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $action = ['Read', 'Create' , 'Update' , 'Delete'];
        $permissions = ['Concession','Order', 'User'];

        $insert_data = [];
        foreach ($permissions as $key => $value) {
            foreach ($action as $act_key => $act_value) {
                $insert_data[] = [
                    'name' => $act_value . "_" . $value,
                    'guard_name' => 'web',
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ];
            }
        }
        
        $customPermissions = ['Approve_Order', 'Complete_Order'];

        foreach ($customPermissions as $permission) {
            $insert_data[] = [
                'name' => $permission,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('permissions')->insert($insert_data);
    }
}
