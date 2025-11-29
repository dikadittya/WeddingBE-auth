<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Lauthz\Facades\Enforcer;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing policies first
        Enforcer::clearPolicy();

        // Define roles
        $roles = ['super_admin', 'admin', 'user', 'guest'];

        // ========================================
        // SUPER ADMIN - Full access to everything
        // ========================================
        
        // Users Management
        Enforcer::addPolicy('super_admin', 'users', 'GET');
        Enforcer::addPolicy('super_admin', 'users', 'POST');
        Enforcer::addPolicy('super_admin', 'users', 'PUT');
        Enforcer::addPolicy('super_admin', 'users', 'PATCH');
        Enforcer::addPolicy('super_admin', 'users', 'DELETE');
        
        // Menus Management
        Enforcer::addPolicy('super_admin', 'menus', 'GET');
        Enforcer::addPolicy('super_admin', 'menus', 'POST');
        Enforcer::addPolicy('super_admin', 'menus', 'PUT');
        Enforcer::addPolicy('super_admin', 'menus', 'PATCH');
        Enforcer::addPolicy('super_admin', 'menus', 'DELETE');
        
        // Guests Management
        Enforcer::addPolicy('super_admin', 'guests', 'GET');
        Enforcer::addPolicy('super_admin', 'guests', 'POST');
        Enforcer::addPolicy('super_admin', 'guests', 'PUT');
        Enforcer::addPolicy('super_admin', 'guests', 'PATCH');
        Enforcer::addPolicy('super_admin', 'guests', 'DELETE');
        
        // Weddings Management
        Enforcer::addPolicy('super_admin', 'weddings', 'GET');
        Enforcer::addPolicy('super_admin', 'weddings', 'POST');
        Enforcer::addPolicy('super_admin', 'weddings', 'PUT');
        Enforcer::addPolicy('super_admin', 'weddings', 'PATCH');
        Enforcer::addPolicy('super_admin', 'weddings', 'DELETE');

        // ========================================
        // ADMIN - Manage weddings, guests, and view users
        // ========================================
        
        // Users - Read only
        Enforcer::addPolicy('admin', 'users', 'GET');
        
        // Menus - Read only
        Enforcer::addPolicy('admin', 'menus', 'GET');
        
        // Guests - Full access
        Enforcer::addPolicy('admin', 'guests', 'GET');
        Enforcer::addPolicy('admin', 'guests', 'POST');
        Enforcer::addPolicy('admin', 'guests', 'PUT');
        Enforcer::addPolicy('admin', 'guests', 'PATCH');
        Enforcer::addPolicy('admin', 'guests', 'DELETE');
        
        // Weddings - Full access except delete
        Enforcer::addPolicy('admin', 'weddings', 'GET');
        Enforcer::addPolicy('admin', 'weddings', 'POST');
        Enforcer::addPolicy('admin', 'weddings', 'PUT');
        Enforcer::addPolicy('admin', 'weddings', 'PATCH');

        // ========================================
        // USER - Standard user permissions
        // ========================================
        
        // Users - View only
        Enforcer::addPolicy('user', 'users', 'GET');
        
        // Guests - View and create
        Enforcer::addPolicy('user', 'guests', 'GET');
        Enforcer::addPolicy('user', 'guests', 'POST');
        
        // Weddings - View only
        Enforcer::addPolicy('user', 'weddings', 'GET');

        // ========================================
        // GUEST - Limited view-only access
        // ========================================
        
        // Weddings - View only
        Enforcer::addPolicy('guest', 'weddings', 'GET');
        
        // Guests - View only
        Enforcer::addPolicy('guest', 'guests', 'GET');

        $this->command->info('Casbin RBAC policies seeded successfully!');
        $this->command->info('Roles: super_admin, admin, user, guest');
    }
}
