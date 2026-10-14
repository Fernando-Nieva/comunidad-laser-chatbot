<?php

namespace App\Chat;

use Illuminate\Support\Collection;

class ChatReply
{
    public function __construct(
        public readonly string $text,
        public readonly string $source, // 'community', 'external_ai', 'local_ai', 'question', 'none', 'external_ai_with_web', 'local_ai_with_web'
        public readonly array $scores = [],
        public readonly array $experienceIds = [],
        public readonly int $fallbackLevel = 0, // 0=primary, 1=fallback1, 2=fallback2, 3=none
        public readonly ?string $fallbackReason = null,
        public readonly array $webSourceUrls = [],
    ) {}

    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'source' => $this->source,
            'scores' => $this->scores,
            'experience_ids' => $this->experienceIds,
            'fallback_level' => $this->fallbackLevel,
            'fallback_reason' => $this->fallbackReason,
            'web_source_urls' => $this->webSourceUrls,
        ];
    }

    public static function question(string $question): self
    {
        return new self(
            text: $question,
            source: 'question',
            fallbackLevel: 0,
        );
    }

    public static function community(string $text, array $scores, array $experienceIds): self
    {
        return new self(
            text: $text,
            source: 'community',
            scores: $scores,
            experienceIds: $experienceIds,
            fallbackLevel: 0,
        );
    }

    public static function externalAi(string $text, int $fallbackLevel = 1, ?string $reason = null): self
    {
        return new self(
            text: $text,
            source: 'external_ai',
            fallbackLevel: $fallbackLevel,
            fallbackReason: $reason,
        );
    }

    public static function localAi(string $text, int $fallbackLevel = 2, ?string $reason = null): self
    {
        return new self(
            text: $text,
            source: 'local_ai',
            fallbackLevel: $fallbackLevel,
            fallbackReason: $reason,
        );
    }

    public static function externalAiWithWeb(string $text, int $fallbackLevel = 1, ?string $reason = null, array $webSourceUrls = []): self
    {
        return new self(
            text: $text,
            source: 'external_ai_with_web',
            fallbackLevel: $fallbackLevel,
            fallbackReason: $reason,
            webSourceUrls: $webSourceUrls,
        );
    }

    public static function localAiWithWeb(string $text, int $fallbackLevel = 2, ?string $reason = null, array $webSourceUrls = []): self
    {
        return new self(
            text: $text,
            source: 'local_ai_with_web',
            fallbackLevel: $fallbackLevel,
            fallbackReason: $reason,
            webSourceUrls: $webSourceUrls,
        );
    }

    public static function none(string $text): self
    {
        return new self(
            text: $text,
            source: 'none',
            fallbackLevel: 3,
            fallbackReason: 'No sources available',
        );
    }
}