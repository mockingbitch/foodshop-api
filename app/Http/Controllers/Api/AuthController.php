<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterOwnerRequest;
use App\Http\Requests\Auth\UpdateOwnerProfileRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Authentication APIs: owner register/login, admin login, logout, me, update profile.
 *
 * @group Authentication
 */
class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Register Restaurant Owner
     * @unauthenticated
     */
    public function registerOwner(RegisterOwnerRequest $request): JsonResponse
    {
        $result = $this->authService->registerOwner($request->validated());

        return response()->json([
            'message' => 'Restaurant owner registered successfully',
            'user' => $result['user'],
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Login as Restaurant Owner
     * @unauthenticated
     */
    public function loginOwner(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginOwner($request->email, $request->password);

        return response()->json([
            'message' => 'Login successful',
            'user' => $result['user'],
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Login as Admin
     * @unauthenticated
     */
    public function loginAdmin(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginAdmin($request->email, $request->password);

        return response()->json([
            'message' => 'Admin login successful',
            'user' => $result['user'],
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout - Revoke current access token
     * @authenticated
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logout successful']);
    }

    /**
     * Get Current User
     * @authenticated
     */
    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->me($request->user());

        return response()->json(['user' => $user]);
    }

    /**
     * Update Owner Profile
     * @authenticated
     */
    public function updateOwnerProfile(UpdateOwnerProfileRequest $request): JsonResponse
    {
        $user = $this->authService->updateOwnerProfile($request->user(), $request->validated());

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }
}
