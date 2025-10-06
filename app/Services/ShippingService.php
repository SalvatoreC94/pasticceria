<?php

namespace App\Services;

class ShippingService
{
    public function fee(int $subtotalCents): int
    {
        $threshold = (int) env('FREE_SHIPPING_THRESHOLD_CENTS', 6900);
        $base      = (int) env('SHIPPING_BASE_CENTS', 1000);
        return $subtotalCents >= $threshold ? 0 : $base;
    }
}
