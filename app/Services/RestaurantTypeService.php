<?php

namespace App\Services;

use App\Contracts\Repositories\RestaurantTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Restaurant type reference data: list active types (General, Snack Bar, Buffet).
 */
class RestaurantTypeService
{
    public function __construct(
        protected RestaurantTypeRepositoryInterface $restaurantTypeRepository
    ) {}

    /**
     * Get all active restaurant types.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return $this->restaurantTypeRepository->getActiveAll();
    }
}
