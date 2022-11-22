<?php

namespace SalesInvoice\Infrastructure;

class Database
{
    /** @var mixed[] */
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

    public function byId()
    {
        return $this->params;
    }
}
