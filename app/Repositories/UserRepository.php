<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;

/**
 * User repository: Eloquent query layer for User model.
 * Handles find by email/role, create, update.
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Find user by email and role.
     *
     * @param string $email
     * @param string $role
     * @return User|null
     */
    public function findByEmailAndRole(string $email, string $role): ?User
    {
        return $this->query()
            ->where('email', $email)
            ->where('role', $role)
            ->first();
    }

    /**
     * Create user with given attributes.
     *
     * @param array $attributes
     * @return User
     */
    public function createUser(array $attributes): User
    {
        return $this->model->create($attributes);
    }

    /**
     * Update user (by model instance).
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    /** Count all users. */
    public function count(): int
    {
        return $this->query()->count();
    }

    /** Count owners. */
    public function countOwners(): int
    {
        return $this->query()->owners()->count();
    }

    /** Count admins. */
    public function countAdmins(): int
    {
        return $this->query()->admins()->count();
    }
}
