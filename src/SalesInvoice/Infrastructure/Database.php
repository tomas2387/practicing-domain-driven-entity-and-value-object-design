<?php

namespace SalesInvoice\Infrastructure;

use SalesInvoice\Domain\Invoice;

class Database
{
    private Invoice $params;

    public function save(Invoice $invoice): void
    {
        $this->params = $invoice;
    }

    public function byId(): Invoice
    {
        return $this->params;
    }
}
