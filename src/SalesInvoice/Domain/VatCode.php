<?php

namespace SalesInvoice\Domain;

use DateTime;
use InvalidArgumentException;

class VatCode
{
    public function __construct(string $vatCode)
    {
        $map = ['S', 'L'];
        if (!in_array($vatCode, $map, true)) {
            throw new InvalidArgumentException('Invalid VAT code');
        }

        $this->vatCode = $vatCode;
    }

    public function rate(): float
    {
        if ($this->vatCode === 'S') {
            return 21.0;
        }

        if ($this->vatCode === 'L') {
            if (new DateTime('now') < DateTime::createFromFormat('Y-m-d', '2019-01-01')) {
                return 6.0;
            }

            return 9.0;
        }

        throw new InvalidArgumentException('Should not happen');
    }
}
