<?php

namespace App\Exceptions;

use Exception;

class PaymentFailedException extends Exception
{
    protected $provider;
    protected $amount;

    public function __construct(string $message, string $provider, $amount = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->provider = $provider;
        $this->amount = $amount;

        parent::__construct($message, $code, $previous);
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getAmount()
    {
        return $this->amount;
    }
}