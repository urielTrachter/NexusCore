<?php

namespace App\Actions;

use App\Factories\NexusFactory;
use App\Services\NexusOrderProcessingService;
use Illuminate\Http\Request;

class ProcessOrderAction
{
    /**
     * Handle the incoming request to process an order.
     */
    public function __invoke(Request $request)
    {
        // Load configured drivers from config
        $envSettings = [
            'PAYMENT_DRIVER' => config('nexus.payment_driver', 'paypal'),
            'INVOICE_DRIVER' => config('nexus.invoice_driver', 'morning'),
            'SMS_DRIVER'     => config('nexus.sms_driver', 'vonage'),
        ];

        // Bootstrapping step (factory creates concrete implementations)
        $paymentDriver = NexusFactory::createPaymentDriver($envSettings['PAYMENT_DRIVER']);
        $invoiceDriver = NexusFactory::createInvoiceDriver($envSettings['INVOICE_DRIVER']);
        $smsDriver     = NexusFactory::createSmsDriver($envSettings['SMS_DRIVER']);

        $service = new NexusOrderProcessingService($paymentDriver, $invoiceDriver, $smsDriver);

        $orderData = $request->only(['amount', 'customer_name', 'customer_phone', 'items']);

        $result = $service->process($orderData);

        return response()->json($result);
    }
}

