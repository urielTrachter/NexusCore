<?php

namespace App\Services\Strategies\Invoice;

use App\Interfaces\InvoiceProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class MorningInvoiceStrategy implements InvoiceProviderInterface
{
    public function generate(array $customerInfo, array $lines): string
    {
        $apiToken = config('nexus.morning_api_token', env('MORNING_API_TOKEN'));
        $apiUrl = config('nexus.morning_api_url', env('MORNING_API_URL', 'https://api.morning.co.il'));

        if (!$apiToken) {
            throw new \Exception('Morning API token not configured');
        }

        try {
            $client = new Client();

            // Prepare payload for Morning API
            $payload = [
                'customer' => [
                    'name' => $customerInfo['name'],
                    'email' => $customerInfo['email'] ?? '',
                    'address' => $customerInfo['address'] ?? '',
                ],
                'items' => array_map(function ($line) {
                    return [
                        'description' => $line['description'],
                        'quantity' => $line['quantity'],
                        'unit_price' => $line['price'],
                        'currency' => 'ILS',
                    ];
                }, $lines),
                'type' => 'invoice',
            ];

            // Real Morning API call
            $response = $client->post($apiUrl . '/invoices', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $data = json_decode($response->getBody(), true);
            $url = $data['invoice_url'] ?? $data['url'] ?? ($data['data']['invoice_url'] ?? '');
            echo "[Morning] Invoice generated for {$customerInfo['name']} with " . count($lines) . " items. URL: {$url}\n";
            return $url;

        } catch (RequestException $e) {
            echo "[Morning] HTTP Error: " . $e->getMessage() . "\n";
            return '';
        } catch (\Exception $e) {
            echo "[Morning] Error: " . $e->getMessage() . "\n";
            return '';
        }
    }
}