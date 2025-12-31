<?php

namespace App\Interfaces;

interface SmsProviderInterface
{
    public function send(string $phone, string $message): bool;
}