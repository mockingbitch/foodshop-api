<?php

namespace App\Contracts\Repositories;

use App\Models\ExchangeRate;
use Illuminate\Database\Eloquent\Collection;

/**
 * Exchange rate repository contract.
 */
interface ExchangeRateRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all exchange rates for a date.
     *
     * @param string $date Y-m-d
     * @return Collection
     */
    public function getByDate(string $date): Collection;

    /**
     * Find rate for from_currency, to_currency and date.
     *
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param string $date
     * @return ExchangeRate|null
     */
    public function findRate(string $fromCurrency, string $toCurrency, string $date): ?ExchangeRate;
}
