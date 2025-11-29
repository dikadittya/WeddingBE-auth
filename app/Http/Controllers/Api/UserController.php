<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $query = User::query();
        $query->with(['member']);
        $users = $query->latest()->paginate($perPage)->appends($request->query())->withPath('');
        
        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully',
            'data' => $users
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = bcrypt($validated['password']);
        $memberId = $validated['member_id'] ?? null;
        unset($validated['member_id']);

        $user = User::create($validated);

        // Optionally attach member relation if provided (Member belongsTo User)
        if ($memberId) {
            $member = \App\Models\Member::find($memberId);
            if ($member) {
                $member->user()->associate($user); // associate works on belongsTo inside Member model
                $member->save();
            }
        }

        $user->load('member');
        
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(string $id): JsonResponse
    {
        $user = User::with(['member'])->find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully',
            'data' => $user
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        $validated = $request->validated();
        // Only allow username, password, and role to be updated (all optional)
        $allowedKeys = ['username', 'password', 'role'];
        $filtered = array_intersect_key($validated, array_flip($allowedKeys));

        // Hash password if provided
        if (isset($filtered['password'])) {
            $filtered['password'] = bcrypt($filtered['password']);
        }

        // Apply updates only for allowed fields
        if (!empty($filtered)) {
            $user->update($filtered);
        }

        $user->load('member');
        
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
