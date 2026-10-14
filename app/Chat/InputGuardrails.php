<?php

namespace App\Chat;

class InputGuardrails
{
    private int $maxLength;
    private array $injectionPatterns;
    private array $offTopicKeywords;

    public function __construct(
        int $maxLength = 1000,
        array $injectionPatterns = [],
        array $offTopicKeywords = [],
    ) {
        $this->maxLength = $maxLength;
        $this->injectionPatterns = $injectionPatterns ?: [
            '/ignora(r|)\s+tus?\s+instrucciones?/i',
            '/olvida(r|)\s+(tus?\s+)?instrucciones?/i',
            '/no\s+sigas\s+tus?\s+instrucciones?/i',
            '/cambia(r|)\s+(tus?\s+)?instrucciones?/i',
            '/system\s+prompt/i',
            '/you\s+are\s+an\s+ai/i',
            '/act\s+as\s+(if\s+)?(you\s+are\s+)?/i',
            '/pretend\s+to\s+be/i',
            '/roleplay/i',
            '/bypass/i',
            '/jailbreak/i',
            '/<\s*system\s*>/i',
            '/\[system\]/i',
            '/instrucciones?/i',
        ];
        $this->offTopicKeywords = $offTopicKeywords ?: [
            'receta', 'cocina', 'comida', 'restaurante',
            'deporte', 'futbol', 'tenis', 'gimnasio',
            'pelicula', 'serie', 'netflix', 'cine',
            'musica', 'cancion', 'artista', 'concierto',
            'politica', 'elecciones', 'gobierno', 'presidente',
            'religion', 'dios', 'iglesia', 'fe',
            'salud', 'medico', 'hospital', 'enfermedad',
            'finanzas', 'bolsa', 'acciones', 'cripto', 'bitcoin',
            'trabajo', 'empleo', 'curriculum', 'entrevista',
            'estudio', 'universidad', 'colegio', 'examen',
            'viaje', 'turismo', 'hotel', 'vuelo',
            'moda', 'ropa', 'zapatos', 'maquillaje',
            'tecnologia', 'celular', 'iphone', 'android', 'app',
        ];
    }

    public function validate(string $message): ?string
    {
        // 1. Check length
        if (mb_strlen($message) > $this->maxLength) {
            return "El mensaje es muy largo (máximo {$this->maxLength} caracteres).";
        }

        // 2. Check for empty/whitespace only
        if (trim($message) === '') {
            return 'El mensaje no puede estar vacío.';
        }

        // 3. Check for prompt injection attempts
        foreach ($this->injectionPatterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return 'Tu mensaje parece intentar modificar las instrucciones del sistema. Por favor, hacé una consulta normal sobre grabado/corte láser.';
            }
        }

        // 4. Basic off-topic check (only for very obvious cases)
        $lowerMessage = mb_strtolower($message);
        foreach ($this->offTopicKeywords as $keyword) {
            // Only reject if the message is SHORT and ONLY contains off-topic content
            if (mb_strlen($message) < 50 && stripos($message, $keyword) !== false) {
                // Check if it's actually a laser-related query that happens to mention the word
                $laserContext = [
                    'láser', 'laser', 'grabar', 'cortar', 'marcar',
                    'mdf', 'acrílico', 'madera', 'cuero', 'pvc',
                    'velocidad', 'potencia', 'pasadas', 'dpi', 'lpi',
                    'parámetros', 'configuración', 'ajustes',
                ];
                $hasLaserContext = false;
                foreach ($laserContext as $ctx) {
                    if (stripos($message, $ctx) !== false) {
                        $hasLaserContext = true;
                        break;
                    }
                }
                if (!$hasLaserContext) {
                    return "Solo puedo ayudarte con temas de grabado, corte y marcado láser. Tu consulta parece ser sobre otro tema.";
                }
            }
        }

        return null; // Valid
    }
}