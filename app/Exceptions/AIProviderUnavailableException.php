<?php

namespace App\Exceptions;

class AIProviderUnavailableException extends \RuntimeException
{
    public static function provider(string $provider): self
    {
        return new self("Proveedor de IA no disponible: {$provider}");
    }
}