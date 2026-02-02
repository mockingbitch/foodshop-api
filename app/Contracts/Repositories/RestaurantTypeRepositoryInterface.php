<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;

/**
 * Restaurant type repository contract.
 */
interface RestaurantTypeRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all active restaurant types.
     *
     * @return Collection
     */
    public function getActiveAll(): Collection;
}
