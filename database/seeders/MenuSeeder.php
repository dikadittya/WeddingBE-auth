<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            // Dashboard
            [
                'id' => 1,
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'icon' => 'dashboard',
                'url' => '/dashboard',
                'parent_id' => null,
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // User Management
            [
                'id' => 2,
                'name' => 'User Management',
                'slug' => 'user-management',
                'icon' => 'people',
                'url' => null,
                'parent_id' => null,
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Users',
                'slug' => 'users',
                'icon' => 'person',
                'url' => '/users',
                'parent_id' => 2,
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Roles & Permissions',
                'slug' => 'roles-permissions',
                'icon' => 'security',
                'url' => '/roles-permissions',
                'parent_id' => 2,
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Member Management
            [
                'id' => 5,
                'name' => 'Member Management',
                'slug' => 'member-management',
                'icon' => 'group',
                'url' => null,
                'parent_id' => null,
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Members',
                'slug' => 'members',
                'icon' => 'person_outline',
                'url' => '/members',
                'parent_id' => 5,
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'Job Descriptions',
                'slug' => 'job-descriptions',
                'icon' => 'work',
                'url' => '/job-descriptions',
                'parent_id' => 5,
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Settings
            [
                'id' => 8,
                'name' => 'Settings',
                'slug' => 'settings',
                'icon' => 'settings',
                'url' => null,
                'parent_id' => null,
                'order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'name' => 'Menu Management',
                'slug' => 'menu-management',
                'icon' => 'menu',
                'url' => '/menu-management',
                'parent_id' => 8,
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'name' => 'System Settings',
                'slug' => 'system-settings',
                'icon' => 'settings_applications',
                'url' => '/system-settings',
                'parent_id' => 8,
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('menus')->insert($menus);

        $this->command->info('Menus seeded successfully!');
    }
}
