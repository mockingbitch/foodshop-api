<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;

/**
 * Country repository contract.
 */
interface CountryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all active countries.
     *
     * @return Collection
     */
    public function getActiveAll(): Collection;
}
