<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Exchange rate model (currency conversion).
 *
 * @property int $id
 * @property string $from_currency
 * @property string $to_currency
 * @property float $rate
 * @property float|null $buy_rate
 * @property float|null $sell_rate
 * @property \Carbon\Carbon $rate_date
 * @property string|null $source
 */
class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_currency',
        'to_currency',
        'rate',
        'buy_rate',
        'sell_rate',
        'rate_date',
        'source',
    ];

    protected $casts = [
        'rate' => 'decimal:6',
        'buy_rate' => 'decimal:6',
        'sell_rate' => 'decimal:6',
        'rate_date' => 'date',
    ];

    /**
     * Get exchange rate for from/to currency and optional date.
     */
    public static function getRate(string $fromCurrency, string $toCurrency, ?string $date = null): ?float
    {
        $date = $date ?? now()->toDateString();
        
        $exchangeRate = self::where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('rate_date', $date)
            ->first();

        return $exchangeRate?->rate;
    }

    /**
     * Convert amount from one currency to another.
     */
    public static function convert(float $amount, string $fromCurrency, string $toCurrency, ?string $date = null): ?float
    {
        $rate = self::getRate($fromCurrency, $toCurrency, $date);
        
        return $rate ? $amount * $rate : null;
    }
}
