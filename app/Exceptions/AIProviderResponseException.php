<?php

namespace App\Exceptions;

class AIProviderResponseException extends \RuntimeException
{
    public static function malformed(string $provider, string $detail = ''): self
    {
        $msg = "Respuesta mal formada del proveedor: {$provider}";
        if ($detail) {
            $msg .= " - {$detail}";
        }
        return new self($msg);
    }

    public static function httpError(string $provider, int $statusCode, string $body = ''): self
    {
        $msg = "Error HTTP {$statusCode} del proveedor: {$provider}";
        if ($body) {
            $msg .= " - {$body}";
        }
        return new self($msg);
    }
}