<?php

namespace App\Services\Strategies\Invoice;

use App\Interfaces\InvoiceProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GreenInvoiceStrategy implements InvoiceProviderInterface
{
    public function generate(array $customerInfo, array $lines): string
    {
        $apiKey = config('nexus.green_invoice_key', env('GREEN_INVOICE_API_KEY'));
        $apiSecret = config('nexus.green_invoice_secret', env('GREEN_INVOICE_API_SECRET'));

        if (!$apiKey || !$apiSecret) {
            throw new \Exception('GreenInvoice credentials not configured');
        }

        try {
            $client = new Client();

            // Prepare payload for GreenInvoice API
            $payload = [
                'type' => 320, // Invoice type
                'description' => 'Order Invoice',
                'client' => [
                    'name' => $customerInfo['name'],
                    'email' => $customerInfo['email'] ?? '',
                    'address' => $customerInfo['address'] ?? '',
                ],
                'income' => array_map(function ($line) {
                    return [
                        'description' => $line['description'],
                        'quantity' => $line['quantity'],
                        'price' => $line['price'],
                        'currency' => 'ILS',
                    ];
                }, $lines),
            ];

            // Real GreenInvoice API call
            $response = $client->post('https://api.greeninvoice.co.il/api/v1/documents', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $data = json_decode($response->getBody(), true);
            $url = $data['url'] ?? $data['invoice_url'] ?? ($data['data']['url'] ?? '');
            echo "[GreenInvoice] Invoice generated for {$customerInfo['name']} with " . count($lines) . " items. URL: {$url}\n";
            return $url;

        } catch (RequestException $e) {
            echo "[GreenInvoice] HTTP Error: " . $e->getMessage() . "\n";
            return '';
        } catch (\Exception $e) {
            echo "[GreenInvoice] Error: " . $e->getMessage() . "\n";
            return '';
        }
    }
}