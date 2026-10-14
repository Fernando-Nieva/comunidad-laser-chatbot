<?php

namespace App\Exceptions;

class AIProviderTimeoutException extends \RuntimeException
{
    public static function provider(string $provider, int $timeout): self
    {
        return new self("Timeout ({$timeout}s) al contactar proveedor: {$provider}");
    }
}