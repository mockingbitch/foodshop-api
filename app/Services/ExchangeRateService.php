<?php

namespace App\Services;

use App\Contracts\Repositories\ExchangeRateRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Exchange rate business logic: get rates by date, convert amount between currencies.
 */
class ExchangeRateService
{
    public function __construct(
        protected ExchangeRateRepositoryInterface $exchangeRateRepository
    ) {}

    /**
     * Get exchange rates for a specific date.
     *
     * @param string|null $date Y-m-d, default today
     * @return array{date: string, rates: Collection}
     */
    public function getExchangeRates(?string $date = null): array
    {
        $date = $date ?? now()->toDateString();
        $rates = $this->exchangeRateRepository->getByDate($date);

        return ['date' => $date, 'rates' => $rates];
    }

    /**
     * Convert amount from one currency to another.
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param string|null $date Optional date for rate
     * @return array{original_amount: float, from_currency: string, to_currency: string, converted_amount: float|null}
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency, ?string $date = null): array
    {
        $date = $date ?? now()->toDateString();
        $exchangeRate = $this->exchangeRateRepository->findRate($fromCurrency, $toCurrency, $date);
        $convertedAmount = $exchangeRate ? (float) ($amount * $exchangeRate->rate) : null;

        if ($convertedAmount === null && $fromCurrency !== $toCurrency) {
            Log::warning('Exchange rate not found, conversion skipped', [
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
                'date' => $date,
            ]);
        }

        return [
            'original_amount' => $amount,
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency,
            'converted_amount' => $convertedAmount,
        ];
    }
}
