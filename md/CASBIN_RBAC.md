# Casbin RBAC Implementation

## Roles
- **super_admin**: Full access to all resources
- **admin**: Can manage weddings and guests
- **guest**: Read-only access

## Usage

### Register with role
```bash
POST /api/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "admin"
}
```

### Protect routes with Casbin
```php
Route::middleware(['auth:sanctum', 'casbin:guests,GET'])->get('/guests', [GuestController::class, 'index']);
Route::middleware(['auth:sanctum', 'casbin:guests,POST'])->post('/guests', [GuestController::class, 'store']);
```

## Setup
1. Run migrations: `php artisan migrate`
2. Seed permissions: `php artisan db:seed --class=RolePermissionSeeder`
