<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Driver
    |--------------------------------------------------------------------------
    |
    | This option determines the payment gateway driver to use for processing payments.
    | Supported: stripe, paypal, etc.
    |
    */

    'payment_driver' => env('NEXUS_PAYMENT_DRIVER', 'stripe'),

    /*
    |--------------------------------------------------------------------------
    | Invoice Driver
    |--------------------------------------------------------------------------
    |
    | This option determines the invoice provider driver to use for generating invoices.
    | Supported: green, morning, etc.
    |
    */

    'invoice_driver' => env('NEXUS_INVOICE_DRIVER', 'green'),

    /*
    |--------------------------------------------------------------------------
    | SMS Driver
    |--------------------------------------------------------------------------
    |
    | This option determines the SMS provider driver to use for sending SMS messages.
    | Supported: twilio, vonage, etc.
    |
    */

    'sms_driver' => env('NEXUS_SMS_DRIVER', 'twilio'),

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configurations
    |--------------------------------------------------------------------------
    */

    'stripe_publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
    'stripe_secret_key' => env('STRIPE_SECRET_KEY'),

    'paypal_client_id' => env('PAYPAL_CLIENT_ID'),
    'paypal_client_secret' => env('PAYPAL_CLIENT_SECRET'),
    'paypal_mode' => env('PAYPAL_MODE', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Invoice Provider Configurations
    |--------------------------------------------------------------------------
    */

    'green_invoice_key' => env('GREEN_INVOICE_API_KEY'),
    'green_invoice_secret' => env('GREEN_INVOICE_API_SECRET'),

    'morning_api_token' => env('MORNING_API_TOKEN'),
    'morning_api_url' => env('MORNING_API_URL', 'https://api.morning.co.il'),

    /*
    |--------------------------------------------------------------------------
    | SMS Provider Configurations
    |--------------------------------------------------------------------------
    */

    'twilio_sid' => env('TWILIO_SID'),
    'twilio_token' => env('TWILIO_TOKEN'),
    'twilio_from' => env('TWILIO_FROM'),

    'vonage_api_key' => env('VONAGE_API_KEY'),
    'vonage_api_secret' => env('VONAGE_API_SECRET'),
    'vonage_from' => env('VONAGE_FROM', 'Nexus'),
    // Queue / retry settings
    'payment_poll_attempts' => env('NEXUS_PAYMENT_POLL_ATTEMPTS', 5),
    'payment_poll_delay' => env('NEXUS_PAYMENT_POLL_DELAY', 30), // seconds between attempts
    'invoice_job_attempts' => env('NEXUS_INVOICE_JOB_ATTEMPTS', 3),
    'sms_job_attempts' => env('NEXUS_SMS_JOB_ATTEMPTS', 3),
];