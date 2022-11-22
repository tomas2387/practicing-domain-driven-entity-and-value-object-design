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
    private VatCode $vatCode;
    private ?float $exchangeRate;

    public function __construct(
        int $productId,
        string $description,
        float $quantity,
        int $quantityPrecision,
        float $tariff,
        string $currency,
        ?Discount $discount,
        VatCode $vatCode,
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

        return $this->discount->apply($this->amount());
    }

    public function netAmount(): float
    {
        return round($this->amount() - $this->discountAmount(), 2);
    }

    public function vatAmount(): float
    {
        $vatRate = $this->vatCode->rate();
        return round($this->netAmount() * $vatRate / 100, 2);
    }

    public function productId(): int
    {
        return $this->productId;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function quantity(): float
    {
        return $this->quantity;
    }

    public function quantityPrecision(): int
    {
        return $this->quantityPrecision;
    }

    public function vatCode(): VatCode
    {
        return $this->vatCode;
    }
}
