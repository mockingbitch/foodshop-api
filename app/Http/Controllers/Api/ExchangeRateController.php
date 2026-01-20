<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
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
