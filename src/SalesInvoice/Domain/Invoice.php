<?php
declare(strict_types=1);

namespace SalesInvoice\Domain;

use Assert\Assertion;
use DateTimeImmutable;

final class Invoice
{
    /**
     * @var int
     */
    private $customerId;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var float|null
     */
    private $exchangeRate;

    /**
     * @var int
     */
    private $quantityPrecision;

    /**
     * @var Line[]
     */
    private $lines = [];

    /**
     * @var bool
     */
    private $isFinalized = false;

    /**
     * @var bool
     */
    private $isCancelled = false;

    /**
     * @var DateTimeImmutable
     */
    private $invoiceDate;

    /**
     * @param int $customerId
     * @param string $currency
     * @param float $exchangeRate
     * @param int $quantityPrecision
     * @param array<array-key, Line> $lines
     * @param bool $isFinalized
     * @param bool $isCancelled
     * @param DateTimeImmutable $invoiceDate
     */
    public function __construct(
        int               $customerId,
        string            $currency,
        float             $exchangeRate,
        int               $quantityPrecision,
        array             $lines,
        bool              $isFinalized,
        bool              $isCancelled,
        DateTimeImmutable $invoiceDate
    )
    {
        $this->customerId = $customerId;
        $this->currency = $currency;
        $this->exchangeRate = $exchangeRate;
        $this->quantityPrecision = $quantityPrecision;
        $this->lines = $lines;
        $this->isFinalized = $isFinalized;
        $this->isCancelled = $isCancelled;
        $this->invoiceDate = $invoiceDate;
    }

    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function setInvoiceDate(DateTimeImmutable $invoiceDate): void
    {
        $this->invoiceDate = $invoiceDate;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function setExchangeRate(?float $exchangeRate): void
    {
        $this->exchangeRate = $exchangeRate;
    }

    public function setQuantityPrecision(int $quantityPrecision): void
    {
        $this->quantityPrecision = $quantityPrecision;
    }

    public function addLine(
        int $productId,
        string $description,
        float $quantity,
        float $tariff,
        ?float $discount,
        string $vatCode
    ): void {
        Assertion::inArray($vatCode, ['S', 'L']);

        $this->lines[] = new Line(
            $productId,
            $description,
            $quantity,
            $this->quantityPrecision,
            $tariff,
            $this->currency,
            $discount,
            $vatCode,
            $this->exchangeRate
        );
    }

    public function totalNetAmount(): float
    {
        $sum = 0.0;

        foreach ($this->lines as $line) {
            $sum += $line->netAmount();
        }

        return round($sum, 2);
    }

    public function totalNetAmountInLedgerCurrency(): float
    {
        if ($this->currency === 'EUR' || $this->exchangeRate == null) {
            return $this->totalNetAmount();
        }

        return round($this->totalNetAmount() / $this->exchangeRate, 2);
    }

    public function totalVatAmount(): float
    {
        $sum = 0.0;

        foreach ($this->lines as $line) {
            $sum += $line->vatAmount();
        }

        return round($sum, 2);
    }

    public function totalVatAmountInLedgerCurrency(): float
    {
        if ($this->currency === 'EUR' || $this->exchangeRate == null) {
            return $this->totalVatAmount();
        }

        return round($this->totalVatAmount() / $this->exchangeRate, 2);
    }

    public function setFinalized(bool $finalized): void
    {
        $this->isFinalized = $finalized;
    }

    public function isFinalized(): bool
    {
        return $this->isFinalized;
    }

    public function setCancelled(bool $cancelled): void
    {
        $this->isCancelled = $cancelled;
    }

    public function isCancelled(): bool
    {
        return $this->isCancelled;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function toArray(): array
    {
        return [
            $this->customerId,
            $this->currency,
            $this->exchangeRate,
            $this->quantityPrecision,
            $this->lines,
            $this->isFinalized,
            $this->isCancelled,
            $this->invoiceDate,
        ];
    }
}
