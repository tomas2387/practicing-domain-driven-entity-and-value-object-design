<?php

namespace SalesInvoice;

use PHPUnit\Framework\TestCase;
use SalesInvoice\Application\SalesInvoice;
use SalesInvoice\Infrastructure\Database;

class SalesInvoiceTest extends TestCase
{
    public function test_createInvoice(): void
    {
        $database = new Database();
        $sut = new SalesInvoice($database);
        $sut->createInvoice(
            1001,
            'USD',
            1.3,
            3,
            [],
            false,
            false,
            new \DateTimeImmutable('2022-01-01 12:00:00')
        );
        $this->assertEquals(
            [
                1001,
                'USD',
                1.3,
                3.0,
                [],
                false,
                false,
                new \DateTimeImmutable('2022-01-01 12:00:00'),
            ],
            $database->byId()->toArray());
    }
}
