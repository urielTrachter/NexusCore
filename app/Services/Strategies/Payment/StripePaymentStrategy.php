<?php

namespace App\Services\Strategies\Payment;

use App\Interfaces\PaymentGatewayInterface;
use Stripe\StripeClient;

class StripePaymentStrategy implements PaymentGatewayInterface
{
    public function charge(float $amount, array $meta = []): bool
    {
        $stripeSecret = config('nexus.stripe_secret_key', env('STRIPE_SECRET_KEY'));

        if (!$stripeSecret) {
            throw new \Exception('Stripe secret key not configured');
        }

        $stripe = new StripeClient($stripeSecret);

        try {
            if (!isset($meta['payment_method_id'])) {
                throw new \InvalidArgumentException('payment_method_id is required for Stripe charge');
            }

            $currency = $meta['currency'] ?? config('nexus.currency', 'usd');

            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => (int)round($amount * 100),
                'currency' => $currency,
                'payment_method' => $meta['payment_method_id'],
                'confirm' => true,
                'description' => $meta['description'] ?? 'Charge from NexusCore',
            ]);

            $success = isset($paymentIntent->status) && $paymentIntent->status === 'succeeded';
            return $success;

        } catch (\Exception $e) {
            echo "[Stripe] Error: " . $e->getMessage() . "\n";
            return false;
        }
    }
}