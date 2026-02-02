<?php

namespace App\Contracts\Repositories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Collection;

/**
 * Language repository contract.
 */
interface LanguageRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all active languages ordered by sort_order.
     *
     * @return Collection
     */
    public function getActiveAll(): Collection;

    /**
     * Find language by code.
     *
     * @param string $code
     * @return Language
     */
    public function findByCode(string $code): Language;
}
