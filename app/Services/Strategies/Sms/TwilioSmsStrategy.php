<?php

namespace App\Services\Strategies\Sms;

use App\Interfaces\SmsProviderInterface;
use Twilio\Rest\Client;

class TwilioSmsStrategy implements SmsProviderInterface
{
    public function send(string $phone, string $message): bool
    {
        $sid = config('nexus.twilio_sid', env('TWILIO_SID'));
        $token = config('nexus.twilio_token', env('TWILIO_TOKEN'));
        $from = config('nexus.twilio_from', env('TWILIO_FROM'));

        if (!$sid || !$token) {
            throw new \Exception('Twilio credentials not configured');
        }

        try {
            $twilio = new Client($sid, $token);

            $messageResponse = $twilio->messages->create($phone, [
                'from' => $from,
                'body' => $message
            ]);

            $status = $messageResponse->status ?? null;
            $success = in_array($status, ['queued', 'sending', 'sent', 'delivered'], true);
            echo "[Twilio] SMS to {$phone} status: {$status}\n";
            return $success;

        } catch (\Exception $e) {
            echo "[Twilio] Error: " . $e->getMessage() . "\n";
            return false;
        }
    }
}