<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Base repository contract: find, create, update, delete.
 * Repository interfaces extend this for common CRUD operations.
 */
interface BaseRepositoryInterface
{
    /**
     * Get the Eloquent model instance.
     */
    public function getModel(): Model;

    /**
     * Find by ID.
     *
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model;

    /**
     * Find by ID or fail.
     *
     * @param int $id
     * @return Model
     */
    public function findOrFail(int $id): Model;

    /**
     * Create model from array.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update model by ID with data.
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model;

    /**
     * Delete model by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
