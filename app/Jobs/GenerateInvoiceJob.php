<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\Invoice;
use App\Services\OrderProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\SendSmsJob;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    public string $orderId;
    public array $orderData;

    public function __construct(string $orderId, array $orderData)
    {
        $this->orderId = $orderId;
        $this->orderData = $orderData;

        $this->queue = 'default';
        $this->tries = (int) config('nexus.invoice_job_attempts', $this->tries);
    }

    public function handle()
    {
        // Ensure payment completed
        $payment = Payment::where('order_id', $this->orderId)->first();

        if (!$payment || $payment->status !== 'success') {
            $attempts = $this->attempts();
            $max = (int) config('nexus.payment_poll_attempts', 5);
            if ($attempts < $max) {
                $delay = (int) config('nexus.payment_poll_delay', 30);
                $this->release($delay);
                return;
            }

            // mark invoice record as failed for this order
            Invoice::create([
                'order_id' => $this->orderId,
                'customer_name' => $this->orderData['customer_name'] ?? '',
                'items' => $this->orderData['items'] ?? [],
                'url' => '',
                'status' => 'payment_not_confirmed',
            ]);
            return;
        }

        // Generate invoice via the configured invoice provider
        $invoiceService = app(\App\Interfaces\InvoiceProviderInterface::class);

        try {
            $url = $invoiceService->generate(
                ['name' => $this->orderData['customer_name']],
                $this->orderData['items'] ?? []
            );

            Invoice::create([
                'order_id' => $this->orderId,
                'customer_name' => $this->orderData['customer_name'] ?? '',
                'items' => $this->orderData['items'] ?? [],
                'url' => $url,
                'status' => $url ? 'generated' : 'failed',
            ]);

            // Dispatch SendSmsJob with invoice URL
            if ($url) {
                SendSmsJob::dispatch($this->orderId, $this->orderData['customer_phone'] ?? '', $url)
                    ->onQueue('default');
            }

        } catch (\Exception $e) {
            Invoice::create([
                'order_id' => $this->orderId,
                'customer_name' => $this->orderData['customer_name'] ?? '',
                'items' => $this->orderData['items'] ?? [],
                'url' => '',
                'status' => 'failed',
            ]);
        }
    }
}
