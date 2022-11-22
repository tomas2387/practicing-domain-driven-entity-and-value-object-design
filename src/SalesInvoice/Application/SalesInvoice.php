<?php
declare(strict_types=1);

namespace SalesInvoice\Application;

use Assert\Assertion;
use SalesInvoice\Infrastructure\Database;

final class SalesInvoice
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function saveInvoice(
        $customerId,
        $currency,
        $exchangeRate,
        $quantityPrecision,
        array $lines,
        bool $isFinalized,
        bool $isCancelled,
        $invoiceDate
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
