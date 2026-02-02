<?php

namespace App\Services;

use App\Contracts\Repositories\LanguageRepositoryInterface;
use App\Models\Language;
use Illuminate\Database\Eloquent\Collection;

/**
 * Language reference data: list active languages, show by code.
 */
class LanguageService
{
    public function __construct(
        protected LanguageRepositoryInterface $languageRepository
    ) {}

    /**
     * Get all active languages ordered by sort_order.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return $this->languageRepository->getActiveAll();
    }

    /**
     * Get language by code.
     *
     * @param string $code
     * @return Language
     */
    public function show(string $code): Language
    {
        return $this->languageRepository->findByCode($code);
    }
}
