<?php

namespace SalesInvoice\Infrastructure;

class Database
{
    /** @var array<array-key, mixed> */
    private array $params;

    /**
     * @param string $sql
     * @param array<array-key, mixed> $params
     * @return void
     */
    public function save(string $sql, array $params): void
    {
        $this->params = $params;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function byId(): array
    {
        return $this->params;
    }
}
