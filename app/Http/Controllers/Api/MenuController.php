<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuRole;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    /**
     * Display a listing of menus.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $isActive = $request->get('is_active');
        $parentOnly = $request->get('parent_only');
        $roleName = $request->get('role');
        
        $query = Menu::with(['parent', 'children', 'roles']);
        
        // Filter by active status
        if ($isActive !== null) {
            $query->where('is_active', $isActive == '1' || $isActive === 'true');
        }
        
        // Filter parent menus only
        if ($parentOnly == '1' || $parentOnly === 'true') {
            $query->parentOnly();
        }
        
        // Filter by role
        if ($roleName) {
            $query->byRole($roleName);
        }
        
        $menus = $query->orderBy('order')->orderBy('id')->paginate($perPage)->appends($request->query())->withPath('');
        
        return response()->json([
            'success' => true,
            'message' => 'Menus retrieved successfully',
            'data' => $menus
        ]);
    }

    /**
     * Store a newly created menu.
     */
    public function store(StoreMenuRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $validated = $request->validated();
            $roles = $validated['roles'] ?? [];
            unset($validated['roles']);
            
            // Set default values
            $validated['order'] = $validated['order'] ?? 0;
            $validated['is_active'] = $validated['is_active'] ?? true;
            
            $menu = Menu::create($validated);
            
            // Assign roles to menu
            if (!empty($roles)) {
                foreach ($roles as $roleName) {
                    MenuRole::create([
                        'menu_id' => $menu->id,
                        'role_name' => $roleName,
                    ]);
                }
            }
            
            $menu->load(['parent', 'children', 'roles']);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Menu created successfully',
                'data' => $menu
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified menu.
     */
    public function show(string $id): JsonResponse
    {
        $menu = Menu::with(['parent', 'children', 'roles'])->find($id);
        
        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Menu retrieved successfully',
            'data' => $menu
        ]);
    }

    /**
     * Update the specified menu.
     */
    public function update(UpdateMenuRequest $request, string $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $menu = Menu::find($id);
            
            if (!$menu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu not found'
                ], 404);
            }
            
            $validated = $request->validated();
            $roles = $validated['roles'] ?? null;
            unset($validated['roles']);
            
            $menu->update($validated);
            
            // Update roles if provided
            if ($roles !== null) {
                // Delete existing roles
                MenuRole::where('menu_id', $menu->id)->delete();
                
                // Assign new roles
                if (!empty($roles)) {
                    foreach ($roles as $roleName) {
                        MenuRole::create([
                            'menu_id' => $menu->id,
                            'role_name' => $roleName,
                        ]);
                    }
                }
            }
            
            $menu->load(['parent', 'children', 'roles']);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Menu updated successfully',
                'data' => $menu
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified menu.
     */
    public function destroy(string $id): JsonResponse
    {
        $menu = Menu::find($id);
        
        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found'
            ], 404);
        }
        
        $menu->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Menu deleted successfully'
        ]);
    }

    /**
     * Get menu tree structure.
     */
    public function tree(Request $request): JsonResponse
    {
        $roleName = $request->get('role');
        $isActive = $request->get('is_active', true);
        
        $query = Menu::with(['children.roles'])
            ->whereNull('parent_id')
            ->where('is_active', $isActive)
            ->orderBy('order');
        
        if ($roleName) {
            $query->byRole($roleName);
        }
        
        $menus = $query->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Menu tree retrieved successfully',
            'data' => $menus
        ]);
    }

    /**
     * Get sidebar menu for authenticated user based on their role.
     */
    public function sidebar(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $userRole = $user->role;
            
            if (!$userRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'User role not found'
                ], 404);
            }
            
            // Get parent menus that belong to user's role and are active
            $parentMenus = Menu::with(['children' => function($query) use ($userRole) {
                    $query->where('is_active', true)
                        ->whereHas('roles', function($q) use ($userRole) {
                            $q->where('role_name', $userRole);
                        })
                        ->orderBy('order');
                }, 'children.roles'])
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->whereHas('roles', function($q) use ($userRole) {
                    $q->where('role_name', $userRole);
                })
                ->orderBy('order')
                ->get();
            
            // Transform the data for sidebar format
            $sidebarMenus = $parentMenus->map(function($menu) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'slug' => $menu->slug,
                    'icon' => $menu->icon,
                    'url' => $menu->url,
                    'order' => $menu->order,
                    'children' => $menu->children->map(function($child) {
                        return [
                            'id' => $child->id,
                            'name' => $child->name,
                            'slug' => $child->slug,
                            'icon' => $child->icon,
                            'url' => $child->url,
                            'order' => $child->order,
                        ];
                    })
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Sidebar menu retrieved successfully',
                'data' => [
                    'user_role' => $userRole,
                    'menus' => $sidebarMenus
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sidebar menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
