<?php

namespace SalesInvoice\Domain;

class Status
{
    private string $status;

    public function __construct(string $status)
    {
        $validStatus = ['CANCELLED', 'FINALIZED', 'DRAFT'];
        if (!in_array($status, $validStatus, true)) {
            throw new \InvalidArgumentException('Invalid status');
        }

        $this->status = $status;
    }

    public function cancel(): void
    {
        if ($this->status === 'CANCELLED') {
            throw new \InvalidArgumentException('Invoice is already cancelled');
        }

        $this->status = 'CANCELLED';
    }

    public function finalize(): void
    {
        if ($this->status === 'FINALIZED') {
            throw new \InvalidArgumentException('Invoice is already finalized');
        }

        $this->status = 'FINALIZED';
    }

    public function isFinalized(): bool
    {
        return $this->status === 'FINALIZED';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'CANCELLED';
    }
}
