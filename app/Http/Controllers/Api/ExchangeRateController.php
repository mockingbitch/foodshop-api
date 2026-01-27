<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

/**
 * @group Exchange Rates
 * APIs for currency exchange rates
 */
class ExchangeRateController extends Controller
{
    /**
     * Get Exchange Rates
     * 
     * Retrieve exchange rates for a specific date
     * 
     * @queryParam date Date for exchange rates (YYYY-MM-DD). Example: 2024-01-27
     */
    public function getExchangeRates(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $rates = ExchangeRate::where('rate_date', $date)
            ->get();

        return response()->json([
            'date' => $date,
            'rates' => $rates,
        ]);
    }

    /**
     * Convert Currency
     * 
     * Convert amount from one currency to another
     * 
     * @bodyParam amount float required Amount to convert. Example: 100.00
     * @bodyParam from_currency string required Source currency code. Example: USD
     * @bodyParam to_currency string required Target currency code. Example: VND
     */
    public function convertCurrency(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'from_currency' => 'required|string|max:5',
            'to_currency' => 'required|string|max:5',
        ]);

        $convertedAmount = ExchangeRate::convert(
            $request->amount,
            $request->from_currency,
            $request->to_currency
        );

        return response()->json([
            'original_amount' => $request->amount,
            'from_currency' => $request->from_currency,
            'to_currency' => $request->to_currency,
            'converted_amount' => $convertedAmount,
        ]);
    }
}
