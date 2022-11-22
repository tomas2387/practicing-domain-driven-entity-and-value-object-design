<?php
declare(strict_types=1);

namespace SalesInvoice\Application;

use Assert\Assertion;
use DateTimeImmutable;
use SalesInvoice\Domain\Invoice;
use SalesInvoice\Domain\Line;
use SalesInvoice\Infrastructure\Database;

final class SalesInvoice
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @param int $customerId
     * @param string $currency
     * @param float $exchangeRate
     * @param int $quantityPrecision
     * @param array<array-key, Line> $lines
     * @param bool $isFinalized
     * @param bool $isCancelled
     * @param DateTimeImmutable $invoiceDate
     * @return void
     */
    public function createInvoice(
        int $customerId,
        string $currency,
        float $exchangeRate,
        int $quantityPrecision,
        array $lines,
        bool $isFinalized,
        bool $isCancelled,
        DateTimeImmutable $invoiceDate
    ): void {
        $invoice = new Invoice(
            $customerId,
            $currency,
            $exchangeRate,
            $quantityPrecision,
            $lines,
            $isFinalized,
            $isCancelled,
            $invoiceDate
        );

        $this->database->save($invoice);
    }

    public function addLinesToInvoice(Line $line): void
    {
        $invoice = $this->database->byId();

        $invoice->addLine(
            $line->productId(),
            $line->description(),
            $line->quantity(),
            $line->quantityPrecision(),
            $line->discountAmount(),
            $line->vatCode()
        );

        $this->database->save($invoice);
    }

    public function finalizeInvoice(): void
    {
        $invoice = $this->database->byId();

        $invoice->setFinalized(true);

        $this->database->save($invoice);
    }

    public function cancelInvoice(): void
    {
        $invoice = $this->database->byId();

        $invoice->setCancelled(true);

        $this->database->save($invoice);
    }
}
