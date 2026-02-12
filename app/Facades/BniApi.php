<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array sendRequest(array $data, string $requestType = null)
 * @method static array createBilling(array $billingData)
 * @method static array updateBilling(array $billingData)
 * @method static array inquiryBilling(array $inquiryData)
 * @method static array inquiryPayment(array $inquiryData)
 * @method static array sendRawRequest(array $data)
 */
class BniApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\BniApiService::class;
    }
}
