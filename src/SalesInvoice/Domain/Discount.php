<?php

namespace SalesInvoice\Domain;

class Discount
{
    private float $discount;

    public function __construct(float $discount)
    {
        if ($discount > 100) {
            throw new \InvalidArgumentException('Discount cannot be greater than 100%');
        }

        $this->discount = $discount;
    }

    public function apply(float $amount): float
    {
        return round($amount * $this->discount / 100, 2);
    }
}
