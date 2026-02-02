<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\ExchangeRate\ConvertCurrencyRequest;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Exchange rates by date and currency conversion (public).
 *
 * @group Exchange Rates
 */
class ExchangeRateController extends BaseApiController
{
    public function __construct(
        protected ExchangeRateService $exchangeRateService
    ) {}

    /**
     * Get exchange rates for a specific date (query: date Y-m-d)
     */
    public function getExchangeRates(Request $request): JsonResponse
    {
        $data = $this->exchangeRateService->getExchangeRates($request->get('date'));

        return $this->success($data);
    }

    /**
     * Convert amount from one currency to another
     */
    public function convertCurrency(ConvertCurrencyRequest $request): JsonResponse
    {
        $data = $this->exchangeRateService->convert(
            $request->amount,
            $request->from_currency,
            $request->to_currency
        );

        return $this->success($data);
    }
}
