<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public static function getRate(string $fromCurrency, string $toCurrency, ?string $date = null): ?float
    {
        $date = $date ?? now()->toDateString();
        
        $exchangeRate = self::where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('rate_date', $date)
            ->first();

        return $exchangeRate?->rate;
    }

    public static function convert(float $amount, string $fromCurrency, string $toCurrency, ?string $date = null): ?float
    {
        $rate = self::getRate($fromCurrency, $toCurrency, $date);
        
        return $rate ? $amount * $rate : null;
    }
}
