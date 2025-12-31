<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\SmsLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    public string $orderId;
    public string $phone;
    public string $invoiceUrl;

    public function __construct(string $orderId, string $phone, string $invoiceUrl)
    {
        $this->orderId = $orderId;
        $this->phone = $phone;
        $this->invoiceUrl = $invoiceUrl;

        $this->queue = 'default';
        $this->tries = (int) config('nexus.sms_job_attempts', $this->tries);
    }

    public function handle()
    {
        $invoice = Invoice::where('order_id', $this->orderId)->first();
        if (!$invoice || $invoice->status !== 'generated' || empty($invoice->url)) {
            $this->release(30);
            return;
        }

        $smsService = app(\App\Interfaces\SmsProviderInterface::class);
        $message = "Hi, your Nexus order is confirmed. Invoice: {$this->invoiceUrl}";

        try {
            $sent = $smsService->send($this->phone, $message);
            SmsLog::create([
                'order_id' => $this->orderId,
                'phone' => $this->phone,
                'message' => $message,
                'status' => $sent ? 'sent' : 'failed',
            ]);
        } catch (\Exception $e) {
            SmsLog::create([
                'order_id' => $this->orderId,
                'phone' => $this->phone,
                'message' => $message,
                'status' => 'failed',
            ]);
            // let the queue attempt retries according to $this->tries
            throw $e;
        }
    }
}
