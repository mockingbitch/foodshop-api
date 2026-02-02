<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
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
class AuthController extends BaseApiController
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

        return $this->created([
            'user' => $result['user'],
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
        ], 'Restaurant owner registered successfully');
    }

    /**
     * Login as Restaurant Owner
     * @unauthenticated
     */
    public function loginOwner(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginOwner($request->email, $request->password);

        return $this->success([
            'user' => $result['user'],
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    /**
     * Login as Admin
     * @unauthenticated
     */
    public function loginAdmin(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginAdmin($request->email, $request->password);

        return $this->success([
            'user' => $result['user'],
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
        ], 'Admin login successful');
    }

    /**
     * Logout - Revoke current access token
     * @authenticated
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(null, 'Logout successful');
    }

    /**
     * Get Current User
     * @authenticated
     */
    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->me($request->user());

        return $this->success(['user' => $user]);
    }

    /**
     * Update Owner Profile
     * @authenticated
     */
    public function updateOwnerProfile(UpdateOwnerProfileRequest $request): JsonResponse
    {
        $user = $this->authService->updateOwnerProfile($request->user(), $request->validated());

        return $this->success(['user' => $user], 'Profile updated successfully');
    }
}
