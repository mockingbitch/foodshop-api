<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Authentication business logic: register owner, login (owner/admin), logout, me, update profile.
 * Uses JWT (tymon/jwt-auth) for API tokens.
 */
class AuthService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    /**
     * Register a new restaurant owner and issue JWT token.
     *
     * @param array $data Validated: name, email, password, phone, address?, country_id?
     * @return array{user: User, token: string}
     */
    public function registerOwner(array $data): array
    {
        $user = $this->userRepository->createUser([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'restaurant_owner',
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'country_id' => $data['country_id'] ?? null,
            'is_active' => true,
        ]);

        $token = JWTAuth::fromUser($user);

        Log::info('Owner registered', ['user_id' => $user->id, 'email' => $user->email]);

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Authenticate restaurant owner by email/password. Throws ValidationException on failure.
     *
     * @param string $email
     * @param string $password
     * @return array{user: User, token: string}
     * @throws ValidationException
     */
    public function loginOwner(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmailAndRole($email, 'restaurant_owner');

        if (!$user || !Hash::check($password, $user->password)) {
            Log::warning('Owner login failed: invalid credentials', ['email' => $email]);
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        if (!$user->is_active) {
            Log::warning('Owner login failed: inactive account', ['user_id' => $user->id]);
            throw ValidationException::withMessages(['email' => ['Your account is inactive. Please contact support.']]);
        }

        $token = JWTAuth::fromUser($user);

        Log::info('Owner logged in', ['user_id' => $user->id]);

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Authenticate admin by email/password. Throws ValidationException on failure.
     *
     * @param string $email
     * @param string $password
     * @return array{user: User, token: string}
     * @throws ValidationException
     */
    public function loginAdmin(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmailAndRole($email, 'admin');

        if (!$user || !Hash::check($password, $user->password)) {
            Log::warning('Admin login failed: invalid credentials', ['email' => $email]);
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        if (!$user->is_active) {
            Log::warning('Admin login failed: inactive account', ['user_id' => $user->id]);
            throw ValidationException::withMessages(['email' => ['Your account is inactive.']]);
        }

        $token = JWTAuth::fromUser($user);

        Log::info('Admin logged in', ['user_id' => $user->id]);

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Invalidate current JWT token (blacklist).
     */
    public function logout(User $user): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        Log::info('User logged out', ['user_id' => $user->id]);
    }

    /**
     * Get authenticated user with country relation.
     */
    public function me(User $user): User
    {
        return $user->load('country');
    }

    /**
     * Update owner profile (name, phone, address, country_id).
     *
     * @param User $user
     * @param array $data Validated: name?, phone?, address?, country_id?
     * @return User
     */
    public function updateOwnerProfile(User $user, array $data): User
    {
        $updated = $this->userRepository->updateUser(
            $user,
            array_intersect_key($data, array_flip(['name', 'phone', 'address', 'country_id']))
        )->load('country');
        Log::info('Owner profile updated', ['user_id' => $user->id]);
        return $updated;
    }
}
