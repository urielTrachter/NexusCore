<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use App\Interfaces\InvoiceProviderInterface;
use App\Interfaces\SmsProviderInterface;

class NexusOrderProcessingService extends OrderProcessingService
{
    public function __construct(
        PaymentGatewayInterface $payment,
        InvoiceProviderInterface $invoice,
        SmsProviderInterface $sms
    ) {
        parent::__construct($payment, $invoice, $sms);
    }

    protected function getSmsMessage(string $invoiceUrl): string
    {
        return "Hi, your Nexus order is confirmed. Invoice: {$invoiceUrl}";
    }
}