<?php

namespace App\Factories;

use App\Interfaces\PaymentGatewayInterface;
use App\Interfaces\InvoiceProviderInterface;
use App\Interfaces\SmsProviderInterface;
use App\Services\Strategies\Payment\StripePaymentStrategy;
use App\Services\Strategies\Payment\PaypalPaymentStrategy;
use App\Services\Strategies\Invoice\GreenInvoiceStrategy;
use App\Services\Strategies\Invoice\MorningInvoiceStrategy;
use App\Services\Strategies\Sms\TwilioSmsStrategy;
use App\Services\Strategies\Sms\VonageSmsStrategy;

class NexusFactory
{
    // Factory methods to create driver instances based on the configured driver type from config/nexus.php
    public static function createPaymentDriver(string $driver): PaymentGatewayInterface
    {
        return match ($driver) {
            'paypal' => new PaypalPaymentStrategy(),
            default  => new StripePaymentStrategy(), // 'stripe'
        };
    }

    public static function createInvoiceDriver(string $driver): InvoiceProviderInterface
    {
        return match ($driver) {
            'morning' => new MorningInvoiceStrategy(),
            default   => new GreenInvoiceStrategy(),
        };
    }

    public static function createSmsDriver(string $driver): SmsProviderInterface
    {
        return match ($driver) {
            'vonage' => new VonageSmsStrategy(),
            default  => new TwilioSmsStrategy(),
        };
    }
}