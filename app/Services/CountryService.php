<?php

namespace App\Services;

use App\Models\Country;
use App\Repositories\CountryRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Country reference data: list active countries, show by ID.
 */
class CountryService
{
    public function __construct(
        protected CountryRepositoryInterface $countryRepository
    ) {}

    /**
     * Get all active countries.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return $this->countryRepository->getActiveAll();
    }

    /**
     * Get country by ID.
     *
     * @param int $id
     * @return Country
     */
    public function show(int $id): Country
    {
        return $this->countryRepository->findOrFail($id);
    }
}
