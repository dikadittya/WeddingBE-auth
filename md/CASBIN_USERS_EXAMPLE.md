# Contoh Penerapan Casbin Middleware untuk CRUD Users

## Struktur Permission yang Diterapkan

### Role Hierarchy
```
super_admin (Full Access)
    ├── admin (Read/Write except delete users)
    ├── user (Read only + limited create)
    └── guest (View only)
```

## Permission Matrix untuk CRUD Users

| Role         | GET (List/View) | POST (Create) | PUT/PATCH (Update) | DELETE |
|-------------|----------------|---------------|-------------------|---------|
| super_admin | ✅             | ✅            | ✅                | ✅      |
| admin       | ✅             | ❌            | ❌                | ❌      |
| user        | ✅             | ❌            | ❌                | ❌      |
| guest       | ❌             | ❌            | ❌                | ❌      |

## Implementasi di Routes (`routes/api.php`)

```php
// Users CRUD with Casbin authorization
Route::middleware('auth:sanctum')->group(function () {
    // GET - List all users (super_admin, admin, user dapat akses)
    Route::middleware('casbin:users,GET')->get('/users', [UserController::class, 'index']);
    
    // GET - Show single user (super_admin, admin, user dapat akses)
    Route::middleware('casbin:users,GET')->get('/users/{user}', [UserController::class, 'show']);
    
    // POST - Create new user (hanya super_admin)
    Route::middleware('casbin:users,POST')->post('/users', [UserController::class, 'store']);
    
    // PUT - Update user (hanya super_admin)
    Route::middleware('casbin:users,PUT')->put('/users/{user}', [UserController::class, 'update']);
    
    // PATCH - Partial update user (hanya super_admin)
    Route::middleware('casbin:users,PATCH')->patch('/users/{user}', [UserController::class, 'update']);
    
    // DELETE - Delete user (hanya super_admin)
    Route::middleware('casbin:users,DELETE')->delete('/users/{user}', [UserController::class, 'destroy']);
});
```

## Cara Kerja Middleware

### Format Middleware
```php
Route::middleware('casbin:resource,action')
```

**Parameters:**
- `resource`: Nama resource yang akan diproteksi (contoh: users, guests, weddings)
- `action`: HTTP method yang diizinkan (GET, POST, PUT, PATCH, DELETE)

### Contoh Flow Authentication

1. **User Login** → Mendapat token + role tersimpan di database
2. **Request ke Endpoint** → Mengirim Bearer token
3. **Casbin Middleware Check**:
   ```php
   // Ambil role user dari database
   $role = auth()->user()->role; // 'super_admin', 'admin', 'user', atau 'guest'
   
   // Cek permission di database casbin_rules
   Enforcer::enforce($role, 'users', 'GET'); // true/false
   ```
4. **Response**:
   - ✅ Jika authorized → Request diteruskan ke controller
   - ❌ Jika unauthorized → Return 403 Forbidden

## Setup & Testing

### 1. Jalankan Migration & Seeder
```bash
php artisan migrate:fresh --seed
```

### 2. Buat User dengan Role Berbeda
```bash
php artisan tinker
```

```php
// Create super_admin
User::create([
    'name' => 'Super Admin',
    'email' => 'superadmin@example.com',
    'password' => bcrypt('password123'),
    'role' => 'super_admin'
]);

// Create admin
User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password123'),
    'role' => 'admin'
]);

// Create regular user
User::create([
    'name' => 'Regular User',
    'email' => 'user@example.com',
    'password' => bcrypt('password123'),
    'role' => 'user'
]);

// Create guest
User::create([
    'name' => 'Guest User',
    'email' => 'guest@example.com',
    'password' => bcrypt('password123'),
    'role' => 'guest'
]);
```

### 3. Test dengan Postman

#### A. Login sebagai super_admin
```http
POST http://127.0.0.1:8000/api/login
Content-Type: application/json

{
    "email": "superadmin@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "token": "1|abc123...",
    "user": {
        "id": 1,
        "name": "Super Admin",
        "role": "super_admin"
    }
}
```

#### B. Test GET /users (Semua role bisa - kecuali guest)
```http
GET http://127.0.0.1:8000/api/users
Authorization: Bearer {token}
```

**✅ super_admin, admin, user** → Success 200
**❌ guest** → Error 403 Unauthorized

#### C. Test POST /users (Hanya super_admin)
```http
POST http://127.0.0.1:8000/api/users
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "New User",
    "email": "newuser@example.com",
    "password": "password123",
    "role": "user"
}
```

**✅ super_admin** → Success 201 Created
**❌ admin, user, guest** → Error 403 Unauthorized

#### D. Test PUT /users/{id} (Hanya super_admin)
```http
PUT http://127.0.0.1:8000/api/users/5
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Updated Name",
    "email": "updated@example.com",
    "role": "admin"
}
```

**✅ super_admin** → Success 200
**❌ admin, user, guest** → Error 403 Unauthorized

#### E. Test DELETE /users/{id} (Hanya super_admin)
```http
DELETE http://127.0.0.1:8000/api/users/5
Authorization: Bearer {token}
```

**✅ super_admin** → Success 200
**❌ admin, user, guest** → Error 403 Unauthorized

## Struktur Response

### Success Response
```json
{
    "success": true,
    "message": "User retrieved successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "admin"
    }
}
```

### Unauthorized Response (403)
```json
{
    "status": "error",
    "message": "Unauthorized access"
}
```

### Unauthenticated Response (401)
```json
{
    "status": "error",
    "message": "Unauthenticated"
}
```

## Casbin Policy Table Structure

Policies disimpan di tabel `user_casbin_rules`:

| id | ptype | v0 (role)    | v1 (resource) | v2 (action) |
|----|-------|--------------|---------------|-------------|
| 1  | p     | super_admin  | users         | GET         |
| 2  | p     | super_admin  | users         | POST        |
| 3  | p     | super_admin  | users         | PUT         |
| 4  | p     | super_admin  | users         | PATCH       |
| 5  | p     | super_admin  | users         | DELETE      |
| 6  | p     | admin        | users         | GET         |
| 7  | p     | user         | users         | GET         |

## Menambah/Mengubah Permission Secara Manual

### Via Tinker
```php
use Lauthz\Facades\Enforcer;

// Tambah policy baru
Enforcer::addPolicy('admin', 'users', 'POST'); // Admin sekarang bisa create user

// Hapus policy
Enforcer::removePolicy('admin', 'users', 'POST'); // Hapus akses create

// Cek policy
Enforcer::enforce('admin', 'users', 'GET'); // true/false

// Lihat semua policy
Enforcer::getAllPolicy();
```

### Via Seeder (Recommended)
Edit file `database/seeders/RolePermissionSeeder.php`, lalu jalankan:
```bash
php artisan db:seed --class=RolePermissionSeeder
```

## Tips & Best Practices

1. **Gunakan Role yang Konsisten**
   - Pastikan field `role` di tabel users sesuai dengan role di Casbin
   - Recommended roles: `super_admin`, `admin`, `user`, `guest`

2. **Resource Naming Convention**
   - Gunakan nama plural: `users`, `guests`, `weddings`
   - Konsisten dengan nama endpoint

3. **Action Mapping**
   - GET → Read/List/View
   - POST → Create
   - PUT/PATCH → Update
   - DELETE → Delete

4. **Granular Control**
   - Bisa membedakan GET list vs GET detail dengan resource berbeda:
     ```php
     Route::middleware('casbin:users.list,GET')->get('/users');
     Route::middleware('casbin:users.detail,GET')->get('/users/{id}');
     ```

5. **Testing Permission**
   - Selalu test dengan berbagai role
   - Pastikan unauthorized access benar-benar di-block

## Troubleshooting

### Error: "Unauthenticated"
- Pastikan token valid dan tidak expired
- Cek header: `Authorization: Bearer {token}`

### Error: "Unauthorized access"
- Role tidak punya permission untuk action tersebut
- Cek `user_casbin_rules` table
- Jalankan ulang seeder jika perlu

### Policy Tidak Ter-apply
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# Re-seed policies
php artisan db:seed --class=RolePermissionSeeder
```

## Kustomisasi Lebih Lanjut

### Menggunakan Resource dari Route Name
```php
// Di routes
Route::middleware('casbin')->name('users.index')->get('/users', [UserController::class, 'index']);

// Middleware akan auto-detect resource dari route name: 'users'
```

### Custom Logic di Middleware
Edit `app/Http/Middleware/CasbinMiddleware.php` untuk menambahkan logic khusus:

```php
// Contoh: Owner dapat edit data sendiri
public function handle(Request $request, Closure $next, $resource = null, $action = null): Response
{
    $user = auth()->user();
    $role = $user->role ?? 'guest';
    
    // Check Casbin policy
    if (!Enforcer::enforce($role, $resource, $action)) {
        // Special case: user dapat update data sendiri
        if ($resource === 'users' && $action === 'PUT') {
            $userId = $request->route('user');
            if ($user->id == $userId) {
                return $next($request); // Allow
            }
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized access'
        ], 403);
    }
    
    return $next($request);
}
```

---

**Catatan:** Dokumentasi ini memberikan contoh lengkap implementasi Casbin RBAC untuk CRUD Users. Anda dapat menerapkan pola yang sama untuk resource lain seperti `guests` atau `weddings`.
