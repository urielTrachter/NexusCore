<?php

namespace App\Services;

use App\Exceptions\PaymentFailedException;
use App\Interfaces\PaymentGatewayInterface;
use App\Interfaces\InvoiceProviderInterface;
use App\Interfaces\SmsProviderInterface;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\SmsLog;
use Illuminate\Support\Str;
use App\Jobs\GenerateInvoiceJob;

abstract class OrderProcessingService
{
    // We perform dependency injection on the interfaces rather than the specific classes
    public function __construct(
        protected PaymentGatewayInterface $payment,
        protected InvoiceProviderInterface $invoice,
        protected SmsProviderInterface $sms
    ) {}

    /**
     * Process an order by charging payment, generating an invoice and sending an SMS notification.
     *
     * @param array{
     *     amount: int|float,
     *     customer_name: string,
     *     items: array,
     *     customer_phone: string
     * } $orderData Structured order data required for processing.
     *
     * @return array{
     *     status: string,
     *     invoice_url: string
     * } Result of the order processing, including the generated invoice URL.
     */
    public function process(array $orderData)
    {
        // Generate unique order ID
        $orderId = Str::uuid()->toString();

        // 1. Charge Payment
        $paid = $this->payment->charge($orderData['amount']);

        // Save payment record
        Payment::create([
            'order_id' => $orderId,
            'amount' => $orderData['amount'],
            'provider' => get_class($this->payment),
            'status' => $paid ? 'success' : 'failed',
        ]);

        if (!$paid) {
            $providerClass = get_class($this->payment);
            $amount = $orderData['amount'] ?? null;
            throw new PaymentFailedException(
                sprintf(
                    'Payment failed via provider %s for amount %s.',
                    $providerClass,
                    (string) $amount
                ),
                $providerClass,
                $amount
            );
        }

        // 2. Queue invoice generation and SMS sending to avoid blocking
        GenerateInvoiceJob::dispatch($orderId, [
            'customer_name' => $orderData['customer_name'],
            'items' => $orderData['items'],
            'customer_phone' => $orderData['customer_phone'] ?? '',
        ])->onQueue('default');

        // Return immediately; invoice and SMS will be handled asynchronously
        return [
            'status' => 'processing',
            'invoice_url' => null,
            'order_id' => $orderId,
        ];

        return [
            'status' => 'success',
            'invoice_url' => $invoiceUrl,
            'order_id' => $orderId,
        ];
    }

    // Abstract method for company-specific SMS message
    abstract protected function getSmsMessage(string $invoiceUrl): string;
}