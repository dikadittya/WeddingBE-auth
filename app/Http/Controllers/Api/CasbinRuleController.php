<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CasbinRuleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lauthz\Facades\Enforcer;

class CasbinRuleController extends Controller
{
    /**
     * Display a listing of Casbin rules.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $ptype = $request->get('ptype'); // Filter by policy type (p or g)
        
        $query = DB::table('user_casbin_rules');
        
        if ($ptype) {
            $query->where('ptype', $ptype);
        }
        
        $rules = $query->orderBy('id')->paginate($perPage)->appends($request->query())->withPath('');
        
        return response()->json([
            'success' => true,
            'message' => 'Casbin rules retrieved successfully',
            'data' => $rules
        ]);
    }

    /**
     * Store a newly created Casbin rule.
     */
    public function store(CasbinRuleRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        try {
            // Add rule using Casbin Enforcer
            if ($validated['ptype'] === 'p') {
                // Policy rule: addPolicy(role/subject, route/object, action)
                $result = Enforcer::addPolicy(
                    $validated['role'],
                    $validated['route'],
                    $validated['action']
                );
            } else {
                // Grouping rule: addRoleForUser(user, role)
                $result = Enforcer::addRoleForUser(
                    $validated['role'],
                    $validated['route'] ?? ''
                );
            }
            
            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rule already exists or could not be added'
                ], 400);
            }
            
            // Get the created rule from database
            $rule = DB::table('user_casbin_rules')
                ->where('ptype', $validated['ptype'])
                ->where('v0', $validated['role'])
                ->where('v1', $validated['route'] ?? '')
                ->where('v2', $validated['action'] ?? '')
                ->first();
                
            return response()->json([
                'success' => true,
                'message' => 'Casbin rule created successfully',
                'data' => $rule
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Casbin rule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified Casbin rule.
     */
    public function show(string $id): JsonResponse
    {
        $rule = DB::table('user_casbin_rules')->find($id);
        
        if (!$rule) {
            return response()->json([
                'success' => false,
                'message' => 'Casbin rule not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Casbin rule retrieved successfully',
            'data' => $rule
        ]);
    }

    /**
     * Update the specified Casbin rule.
     */
    public function update(CasbinRuleRequest $request, string $id): JsonResponse
    {
        $rule = DB::table('user_casbin_rules')->find($id);
        
        if (!$rule) {
            return response()->json([
                'success' => false,
                'message' => 'Casbin rule not found'
            ], 404);
        }
        
        $validated = $request->validated();
        
        try {
            // Remove old rule
            if ($rule->ptype === 'p') {
                Enforcer::removePolicy($rule->v0, $rule->v1, $rule->v2);
            } else {
                Enforcer::deleteRoleForUser($rule->v0, $rule->v1);
            }
            
            // Add new rule with updated values
            $newRule = array_merge((array)$rule, $validated);
            
            if ($newRule['ptype'] === 'p') {
                $result = Enforcer::addPolicy(
                    $newRule['role'] ?? $newRule['v0'],
                    $newRule['route'] ?? $newRule['v1'] ?? '',
                    $newRule['action'] ?? $newRule['v2'] ?? ''
                );
            } else {
                $result = Enforcer::addRoleForUser(
                    $newRule['role'] ?? $newRule['v0'],
                    $newRule['route'] ?? $newRule['v1'] ?? ''
                );
            }
            
            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update rule'
                ], 400);
            }
            
            // Get updated rule
            $updatedRule = DB::table('user_casbin_rules')
                ->where('ptype', $newRule['ptype'])
                ->where('v0', $newRule['role'] ?? $newRule['v0'])
                ->where('v1', $newRule['route'] ?? $newRule['v1'] ?? '')
                ->where('v2', $newRule['action'] ?? $newRule['v2'] ?? '')
                ->first();
            
            return response()->json([
                'success' => true,
                'message' => 'Casbin rule updated successfully',
                'data' => $updatedRule
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Casbin rule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified Casbin rule.
     */
    public function destroy(string $id): JsonResponse
    {
        $rule = DB::table('user_casbin_rules')->find($id);
        
        if (!$rule) {
            return response()->json([
                'success' => false,
                'message' => 'Casbin rule not found'
            ], 404);
        }
        
        try {
            // Remove rule using Casbin Enforcer
            if ($rule->ptype === 'p') {
                $result = Enforcer::removePolicy($rule->v0, $rule->v1, $rule->v2);
            } else {
                $result = Enforcer::deleteRoleForUser($rule->v0, $rule->v1);
            }
            
            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete rule'
                ], 400);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Casbin rule deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Casbin rule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all policies for a specific subject.
     */
    public function getPoliciesForSubject(Request $request, string $subject): JsonResponse
    {
        try {
            $policies = Enforcer::getPermissionsForUser($subject);
            
            return response()->json([
                'success' => true,
                'message' => 'Policies retrieved successfully',
                'data' => [
                    'subject' => $subject,
                    'policies' => $policies
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get policies: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all roles for a specific user.
     */
    public function getRolesForUser(Request $request, string $user): JsonResponse
    {
        try {
            $roles = Enforcer::getRolesForUser($user);
            
            return response()->json([
                'success' => true,
                'message' => 'Roles retrieved successfully',
                'data' => [
                    'user' => $user,
                    'roles' => $roles
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get roles: ' . $e->getMessage()
            ], 500);
        }
    }
}