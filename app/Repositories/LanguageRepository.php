<?php

namespace App\Repositories;

use App\Contracts\Repositories\LanguageRepositoryInterface;
use App\Models\Language;
use Illuminate\Database\Eloquent\Collection;

/**
 * Language repository: Eloquent query layer for Language (reference data).
 * Handles list active (by sort_order), find by code.
 */
class LanguageRepository extends BaseRepository implements LanguageRepositoryInterface
{
    public function __construct(Language $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all active languages ordered by sort_order.
     *
     * @return Collection
     */
    public function getActiveAll(): Collection
    {
        return $this->query()->active()->orderBy('sort_order')->get();
    }

    /**
     * Find language by code.
     *
     * @param string $code
     * @return Language
     */
    public function findByCode(string $code): Language
    {
        return $this->query()->where('code', $code)->firstOrFail();
    }
}
