<?php

namespace App\Repositories;

use App\Contracts\Repositories\RestaurantTypeRepositoryInterface;
use App\Models\RestaurantType;
use Illuminate\Database\Eloquent\Collection;

/**
 * Restaurant type repository: Eloquent query layer for RestaurantType (reference data).
 * Handles list active.
 */
class RestaurantTypeRepository extends BaseRepository implements RestaurantTypeRepositoryInterface
{
    public function __construct(RestaurantType $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all active restaurant types.
     *
     * @return Collection
     */
    public function getActiveAll(): Collection
    {
        return $this->query()->active()->get();
    }
}
