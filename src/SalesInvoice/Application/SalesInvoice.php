<?php
declare(strict_types=1);

namespace SalesInvoice\Application;

use Assert\Assertion;
use DateTimeImmutable;
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
     * @param float $quantityPrecision
     * @param array<array-key, Line> $lines
     * @param bool $isFinalized
     * @param bool $isCancelled
     * @param DateTimeImmutable $invoiceDate
     * @return void
     */
    public function saveInvoice(
        int $customerId,
        string $currency,
        float $exchangeRate,
        float $quantityPrecision,
        array $lines,
        bool $isFinalized,
        bool $isCancelled,
        DateTimeImmutable $invoiceDate
    ): void {
        $this->database->insert('INSERT INTO invoice (customer_id, currency, exchange_rate, quantity_precision, lines, is_finalized, is_cancelled, invoice_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
            $customerId,
            $currency,
            $exchangeRate,
            $quantityPrecision,
            $lines,
            $isFinalized,
            $isCancelled,
            $invoiceDate,
        ]);
    }
}
