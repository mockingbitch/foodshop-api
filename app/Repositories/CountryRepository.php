<?php

namespace App\Repositories;

use App\Contracts\Repositories\CountryRepositoryInterface;
use App\Models\Country;
use Illuminate\Database\Eloquent\Collection;

/**
 * Country repository: Eloquent query layer for Country (reference data).
 * Handles list active, find by ID.
 */
class CountryRepository extends BaseRepository implements CountryRepositoryInterface
{
    public function __construct(Country $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all active countries.
     *
     * @return Collection
     */
    public function getActiveAll(): Collection
    {
        return $this->query()->active()->get();
    }
}
