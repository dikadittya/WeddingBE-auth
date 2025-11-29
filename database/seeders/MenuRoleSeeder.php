<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menuRoles = [
            // Dashboard - All roles
            ['menu_id' => 1, 'role_name' => 'super_admin', 'created_at' => now(), 'updated_at' => now()],
            ['menu_id' => 1, 'role_name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['menu_id' => 1, 'role_name' => 'user', 'created_at' => now(), 'updated_at' => now()],
            
            // User Management - super_admin & admin
            ['menu_id' => 2, 'role_name' => 'super_admin', 'created_at' => now(), 'updated_at' => now()],
            ['menu_id' => 2, 'role_name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            
            // Users - super_admin & admin
            ['menu_id' => 3, 'role_name' => 'super_admin', 'created_at' => now(), 'updated_at' => now()],
            ['menu_id' => 3, 'role_name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            
            // Roles & Permissions - super_admin only
            ['menu_id' => 4, 'role_name' => 'super_admin', 'created_at' => now(), 'updated_at' => now()],
            
            // Member Management - All roles
            ['menu_id' => 5, 'role_name' => 'super_admin', 'created_at' => now(), 'updated_at' => now()],
            ['menu_id' => 5, 'role_name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['menu_id' => 5, 'role_name' => 'user', 'created_at' => now(), 'updated_at' => now()],
            
            // Members - All roles
            ['menu_id' => 6, 'role_name' => 'super_admin', 'created_at' => now(), 'updated_at' => now()],
            ['menu_id' => 6, 'role_name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['menu_id' => 6, 'role_name' => 'user', 'created_at' => now(), 'updated_at' => now()],
            
            // Job Descriptions - super_admin & admin
            ['menu_id' => 7, 'role_name' => 'super_admin', 'created_at' => now(), 'updated_at' => now()],
            ['menu_id' => 7, 'role_name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            
            // Settings - super_admin & admin
            ['menu_id' => 8, 'role_name' => 'super_admin', 'created_at' => now(), 'updated_at' => now()],
            ['menu_id' => 8, 'role_name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            
            // Menu Management - super_admin only
            ['menu_id' => 9, 'role_name' => 'super_admin', 'created_at' => now(), 'updated_at' => now()],
            
            // System Settings - super_admin only
            ['menu_id' => 10, 'role_name' => 'super_admin', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('menu_roles')->insert($menuRoles);

        $this->command->info('Menu roles seeded successfully!');
    }
}
