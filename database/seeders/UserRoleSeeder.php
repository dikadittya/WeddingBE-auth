<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserRole;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Full access to all resources and system settings',
            ],
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Can manage weddings and guests',
            ],
            [
                'name' => 'guest',
                'display_name' => 'Guest',
                'description' => 'Read-only access to wedding information',
            ],
        ];

        foreach ($roles as $role) {
            UserRole::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }

        $this->command->info('User roles seeded successfully!');
    }
}
