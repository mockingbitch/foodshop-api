<?php

namespace App\Contracts\Repositories;

use App\Models\User;

/**
 * User repository contract.
 */
interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find user by email and role.
     *
     * @param string $email
     * @param string $role
     * @return User|null
     */
    public function findByEmailAndRole(string $email, string $role): ?User;

    /**
     * Create user with given attributes.
     *
     * @param array $attributes
     * @return User
     */
    public function createUser(array $attributes): User;

    /**
     * Update user (by model instance).
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User;

    /** Count all users. */
    public function count(): int;

    /** Count owners. */
    public function countOwners(): int;

    /** Count admins. */
    public function countAdmins(): int;
}
