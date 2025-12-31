# NexusCore

A generic payment, invoicing, and SMS service that allows you to dynamically select and add additional providers.

Built with Laravel 12 using Strategy Pattern, Factory Pattern, and Dependency Injection.

## Features

- Payment processing with multiple gateways (Stripe, PayPal)
- Invoice generation with multiple providers (GreenInvoice, Morning)
- SMS sending with multiple providers (Twilio, Vonage)
- Extensible architecture using interfaces and strategies

## Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your settings
4. Run `php artisan serve`

## Usage

Send a POST request to `/order` with order data:

```json
{
  "amount": 150.00,
  "customer_name": "Yossi Cohen",
  "customer_phone": "050-1234567",
  "items": ["item1", "item2"]
}
```

Configure providers in `.env`:

```
NEXUS_PAYMENT_DRIVER=paypal
NEXUS_INVOICE_DRIVER=morning
NEXUS_SMS_DRIVER=vonage
```
