<?php
declare(strict_types=1);

namespace SalesInvoice\Domain;

use DateTime;
use InvalidArgumentException;

final class Line
{
    private int $productId;
    private string $description;
    private float $quantity;
    private int $quantityPrecision;
    private float $tariff;
    private string $currency;
    private ?float $discount;
    private string $vatCode;
    private ?float $exchangeRate;

    public function __construct(
        int $productId,
        string $description,
        float $quantity,
        int $quantityPrecision,
        float $tariff,
        string $currency,
        ?float $discount,
        string $vatCode,
        ?float $exchangeRate
    ) {
        $this->productId = $productId;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->quantityPrecision = $quantityPrecision;
        $this->tariff = $tariff;
        $this->currency = $currency;
        $this->discount = $discount;
        $this->vatCode = $vatCode;
        $this->exchangeRate = $exchangeRate;
    }

    public function amount(): float
    {
        return round(round($this->quantity, $this->quantityPrecision) * $this->tariff, 2);
    }

    public function discountAmount(): float
    {
        if ($this->discount === null) {
            return 0.0;
        }

        return round($this->amount() * $this->discount / 100, 2);
    }

    public function netAmount(): float
    {
        return round($this->amount() - $this->discountAmount(), 2);
    }

    public function vatAmount(): float
    {
        if ($this->vatCode === 'S') {
            $vatRate = 21.0;
        } elseif ($this->vatCode === 'L') {
            if (new DateTime('now') < DateTime::createFromFormat('Y-m-d', '2019-01-01')) {
                $vatRate = 6.0;
            } else {
                $vatRate = 9.0;
            }
        } else {
            throw new InvalidArgumentException('Should not happen');
        }

        return round($this->netAmount() * $vatRate / 100, 2);
    }
}
