<?php
declare(strict_types=1);

namespace SalesInvoice\Domain;

use DateTimeImmutable;

final class Invoice
{
    private int $customerId;
    private string $currency;
    private ?float $exchangeRate;
    private int $quantityPrecision;
    private array $lines = [];
    private Status $status;
    private DateTimeImmutable $invoiceDate;

    /**
     * @param int $customerId
     * @param string $currency
     * @param float $exchangeRate
     * @param int $quantityPrecision
     * @param array<array-key, Line> $lines
     * @param Status $status
     * @param DateTimeImmutable $invoiceDate
     */
    public function __construct(
        int               $customerId,
        string            $currency,
        float             $exchangeRate,
        int               $quantityPrecision,
        array             $lines,
        Status            $status,
        DateTimeImmutable $invoiceDate
    )
    {
        $this->customerId = $customerId;
        $this->currency = $currency;
        $this->exchangeRate = $exchangeRate;
        $this->quantityPrecision = $quantityPrecision;
        $this->lines = $lines;
        $this->invoiceDate = $invoiceDate;
        $this->status = $status;
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
        int     $productId,
        string  $description,
        float   $quantity,
        float   $tariff,
        ?float  $discount,
        VatCode $vatCode
    ): void {

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

    public function changeVatCode(VatCode $vatCode)
    {
        foreach ($this->lines as $line) {
            $line->changeVatCode($vatCode);
        }
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

    public function setFinalized(): void
    {
        $this->status->finalize();
    }

    public function isFinalized(): bool
    {
        return $this->status->isFinalized();
    }

    public function setCancelled(): void
    {
        $this->status->cancel();
    }

    public function isCancelled(): bool
    {
        return $this->status->isCancelled();
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
