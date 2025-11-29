<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|alpha_dash|unique:users,username',
            'email' => 'string|email|max:255|nullable',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|in:super_admin,admin,guest',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'guest',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        $expiresAt = now()->addMinutes(env('TOKEN_EXPIRED', 15));
        $tokenResult = $user->createToken('auth_token', ['*'], $expiresAt);
        
        // Update expires_at manually if needed
        $tokenResult->accessToken->forceFill([
            'expires_at' => $expiresAt,
        ])->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'access_token' => $tokenResult->plainTextToken,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                'expires_in_minutes' => env('TOKEN_EXPIRED', 15),
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Refresh token expiration without changing the token
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user()->select('id', 'name', 'email', 'role')->first();
        $expirationMinutes = env('TOKEN_EXPIRED', 15);
        $currentToken = $request->user()->currentAccessToken();
        
        // Update the token's updated_at to extend expiration
        $currentToken->forceFill([
            'updated_at' => now(),
            'expires_at' => now()->addMinutes($expirationMinutes),
        ])->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Token refreshed successfully',
            'data' => [
                'user' => $user,
                'expires_in_minutes' => $expirationMinutes,
                'new_expiration' => now()->addMinutes($expirationMinutes)->format('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * Get current user with token details
     */
    public function me(Request $request)
    {
        $user = $request->user()->select('id', 'name', 'email', 'role')->first();
        $currentToken = $request->user()->currentAccessToken();
        $tokenCreatedAt = $currentToken->created_at;
        $tokenExpiresAt = $currentToken->expires_at;
        
        // Calculate remaining time in seconds for more accuracy
        $remainingSeconds = now()->diffInSeconds($tokenExpiresAt, false);
        $remainingMinutes = ceil($remainingSeconds / 60);
        
        return response()->json([
            'status' => 'success',
            'message' => 'User details retrieved successfully',
            'data' => [
                'user' => $user,
                'token_info' => [
                    'name' => $currentToken->name,
                    'created_at' => $tokenCreatedAt->format('Y-m-d H:i:s'),
                    'last_used_at' => $currentToken->last_used_at ? $currentToken->last_used_at->format('Y-m-d H:i:s') : null,
                    'expires_at' => $tokenExpiresAt->format('Y-m-d H:i:s'),
                    'remaining_in_minutes' => (int)$remainingMinutes,
                    'remaining_in_seconds' => max(0, (int)$remainingSeconds),
                ]
            ]
        ]);
    }
}