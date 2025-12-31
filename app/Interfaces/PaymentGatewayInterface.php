<?php

namespace App\Interfaces;

interface PaymentGatewayInterface
{
    /**
     * @param float $amount The amount to charge
     * @param array $meta Additional metadata (order_id, customer_email, etc)
     * @return bool True if successful
     */
    public function charge(float $amount, array $meta = []): bool;
}