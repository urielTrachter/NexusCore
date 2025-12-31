<?php

namespace App\Services\Strategies\Sms;

use App\Interfaces\SmsProviderInterface;
use Vonage\Client;

class VonageSmsStrategy implements SmsProviderInterface
{
    public function send(string $phone, string $message): bool
    {
        $apiKey = config('nexus.vonage_api_key', env('VONAGE_API_KEY'));
        $apiSecret = config('nexus.vonage_api_secret', env('VONAGE_API_SECRET'));
        $from = config('nexus.vonage_from', env('VONAGE_FROM', 'Nexus'));

        if (!$apiKey || !$apiSecret) {
            throw new \Exception('Vonage credentials not configured');
        }

        try {
            $client = new Client(new \Vonage\Client\Credentials\Basic($apiKey, $apiSecret));

            $response = $client->sms()->send(new \Vonage\SMS\Message\SMS($phone, $from, $message));

            // response may provide array data via getResponseData() or getMessages(); handle gracefully
            $data = null;
            // Normalize response to array without calling SDK-specific helpers
            $data = null;
            try {
                $data = json_decode(json_encode($response), true);
            } catch (\Throwable $t) {
                $data = null;
            }

            $status = null;
            if (is_array($data)) {
                if (isset($data['messages']) && is_array($data['messages']) && isset($data['messages'][0])) {
                    $msg = $data['messages'][0];
                    $status = $msg['status'] ?? ($msg['statusText'] ?? null);
                } elseif (isset($data[0]) && is_array($data[0])) {
                    $msg = $data[0];
                    $status = $msg['status'] ?? ($msg['statusText'] ?? null);
                }
            }

            $success = $status === '0' || $status === 0 || strtolower((string)$status) === 'delivered' || $status === null;
            echo "[Vonage] SMS to {$phone} status: " . ($status ?? 'unknown') . "\n";
            return $success;

        } catch (\Exception $e) {
            echo "[Vonage] Error: " . $e->getMessage() . "\n";
            return false;
        }
    }
}