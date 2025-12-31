<?php

namespace App\Interfaces;

interface InvoiceProviderInterface
{
    /**
     * @param array $customerInfo Name, Email, Address
     * @param array $lines Line items for the invoice
     * @return string URL or ID of the generated invoice
     */
    public function generate(array $customerInfo, array $lines): string;
}