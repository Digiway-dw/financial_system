<?php

namespace App\Application\UseCases;

use App\Models\Domain\Entities\Customer;

class FindClient
{
    public function byPhone($phone)
    {
        return Customer::where('mobile_number', $phone)->first();
    }

    public function byCode($code)
    {
        return Customer::where('customer_code', $code)->first();
    }
} 