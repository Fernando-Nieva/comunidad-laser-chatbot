<?php

namespace App\Chat;

use Illuminate\Support\Facades\Config;

class PostProcessor
{
    private array $dangerousMaterials;

    public function __construct()
    {
        $this->dangerousMaterials = Config::get('safety.dangerous_materials', []);
    }

    public function process(string $reply, string $source, array $experienceIds = [], array $scores = []): string
    {
        $processed = $reply;

        // 1. Add fixed disclaimer to all responses
        $processed = $this->addFixedDisclaimer($processed);

        // 2. Add source-specific disclaimers
        $processed = $this->addSourceDisclaimer($processed, $source, $experienceIds);

        // 3. Add safety warnings for dangerous materials
        $processed = $this->addSafetyWarnings($processed);

        // 4. Post-process external/local AI responses with parameter values
        if (in_array($source, ['external_ai', 'local_ai'])) {
            $processed = $this->addNonCommunityDisclaimer($processed);
        }

        return $processed;
    }

    private function addFixedDisclaimer(string $reply): string
    {
        $disclaimer = "\n\n---\n**⚠️ Las respuestas son orientativas y no garantizan un resultado. Probá siempre en un retazo antes del trabajo final.**";

        // Don't add if already present
        if (str_contains($reply, 'Las respuestas son orientativas')) {
            return $reply;
        }

        return $reply . $disclaimer;
    }

    private function addSourceDisclaimer(string $reply, string $source, array $experienceIds): string
    {
        $labels = [
            'community' => '📚 **Basado en experiencias de la comunidad',
            'external_ai' => '🌐 **Orientación de IA externa',
            'local_ai' => '💻 **Orientación de IA local',
            'question' => '❓ **Pregunta para completar información',
            'none' => '❌ **Sin información suficiente',
        ];

        $label = $labels[$source] ?? '📝 **Respuesta';

        // Add experience titles for community source
        if ($source === 'community' && !empty($experienceIds)) {
            $label .= ' (IDs: ' . implode(', ', $experienceIds) . ')';
        }

        $label .= '**';

        // Don't add if already present
        if (str_contains($reply, $label)) {
            return $reply;
        }

        // Insert after the first paragraph or at the beginning
        $parts = explode("\n\n", $reply, 2);
        if (count($parts) > 1) {
            return $parts[0] . "\n\n" . $label . "\n\n" . $parts[1];
        }

        return $label . "\n\n" . $reply;
    }

    private function addNonCommunityDisclaimer(string $reply): string
    {
        $disclaimer = "\n\n> **ℹ️ Aclaración**: Los parámetros mencionados provienen de orientación general de IA, **NO de experiencias de la comunidad**. No están validados por usuarios reales. Probá en retazo.";

        if (str_contains($reply, 'NO de experiencias de la comunidad')) {
            return $reply;
        }

        return $reply . $disclaimer;
    }

    private function addSafetyWarnings(string $reply): string
    {
        $warnings = [];

        foreach ($this->dangerousMaterials as $material => $warning) {
            if (stripos($reply, $material) !== false) {
                $warnings[] = $warning;
            }
        }

        if (empty($warnings)) {
            return $reply;
        }

        $warningText = "\n\n" . implode("\n\n", $warnings);

        if (str_contains($reply, $warningText)) {
            return $reply;
        }

        return $reply . $warningText;
    }
}