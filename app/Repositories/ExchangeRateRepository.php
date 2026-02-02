<?php

namespace App\Repositories;

use App\Contracts\Repositories\ExchangeRateRepositoryInterface;
use App\Models\ExchangeRate;
use Illuminate\Database\Eloquent\Collection;

/**
 * Exchange rate repository: Eloquent query layer for ExchangeRate model.
 * Handles get by date, find rate for from/to/date (used by model static methods or service).
 */
class ExchangeRateRepository extends BaseRepository implements ExchangeRateRepositoryInterface
{
    public function __construct(ExchangeRate $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all exchange rates for a date.
     *
     * @param string $date Y-m-d
     * @return Collection
     */
    public function getByDate(string $date): Collection
    {
        return $this->query()->where('rate_date', $date)->get();
    }

    /**
     * Find rate for from_currency, to_currency and date.
     *
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param string $date
     * @return ExchangeRate|null
     */
    public function findRate(string $fromCurrency, string $toCurrency, string $date): ?ExchangeRate
    {
        return $this->query()
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('rate_date', $date)
            ->first();
    }
}
