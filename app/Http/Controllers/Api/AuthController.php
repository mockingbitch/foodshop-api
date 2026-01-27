<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Authentication
 * APIs for authentication and user management
 */
class AuthController extends Controller
{
    /**
     * Register Restaurant Owner
     * 
     * Register a new restaurant owner account
     * 
     * @unauthenticated
     */
    public function registerOwner(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'country_id' => 'nullable|exists:countries,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'restaurant_owner',
            'phone' => $request->phone,
            'address' => $request->address,
            'country_id' => $request->country_id,
            'is_active' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Restaurant owner registered successfully',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Login as Restaurant Owner
     * 
     * Authenticate and get access token for restaurant owner
     * 
     * @unauthenticated
     */
    public function loginOwner(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'restaurant_owner')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account is inactive. Please contact support.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Login as Admin
     * 
     * Authenticate and get access token for admin user
     * 
     * @unauthenticated
     */
    public function loginAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'admin')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account is inactive.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Admin login successful',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout
     * 
     * Revoke the current access token
     * 
     * @authenticated
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Get Current User
     * 
     * Get the authenticated user's information
     * 
     * @authenticated
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('country'),
        ]);
    }

    /**
     * Update Owner Profile
     * 
     * Update the authenticated owner's profile information
     * 
     * @authenticated
     */
    public function updateOwnerProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'nullable|string',
            'country_id' => 'nullable|exists:countries,id',
        ]);

        $user->update($request->only(['name', 'phone', 'address', 'country_id']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->load('country'),
        ]);
    }
}
