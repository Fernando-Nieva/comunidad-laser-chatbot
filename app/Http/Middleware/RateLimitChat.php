<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class RateLimitChat
{
    public function handle(Request $request, Closure $next): Response|SymfonyResponse
    {
        $key = $this->resolveRequestSignature($request);
        $maxAttempts = config('chat.rate_limit.max_attempts', 30);
        $decayMinutes = config('chat.rate_limit.decay_minutes', 1);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($key);

            return response()->json([
                'error' => 'Demasiadas solicitudes. Esperá un momento antes de volver a intentar.',
                'retry_after_seconds' => $retryAfter,
            ], 429)->header('Retry-After', $retryAfter);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', RateLimiter::remaining($key, $maxAttempts));
        $response->headers->set('X-RateLimit-Reset', now()->addMinutes($decayMinutes)->timestamp);

        return $response;
    }

    protected function resolveRequestSignature(Request $request): string
    {
        // Use IP + session ID for identification
        $ip = $request->ip();
        $sessionId = $request->session()->getId() ?? 'no-session';
        
        return "chat:{$ip}:{$sessionId}";
    }
}