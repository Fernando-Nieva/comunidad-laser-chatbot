<?php

namespace App\Chat;

class MissingInfoPolicy
{
    private array $required;
    private array $recommended;
    private int $maxQuestionsInRow;

    public function __construct(
        array $required = ['material', 'job_type'],
        array $recommended = ['thickness_mm', 'machine_power_w'],
        int $maxQuestionsInRow = 3,
    ) {
        $this->required = $required;
        $this->recommended = $recommended;
        $this->maxQuestionsInRow = $maxQuestionsInRow;
    }

    public static function fromConfig(): self
    {
        $required = config('rag.required_fields', ['material', 'job_type']);
        $recommended = config('rag.recommended_fields', ['thickness_mm', 'machine_power_w']);
        $maxQuestions = config('rag.max_questions_in_row', 3);

        return new self($required, $recommended, $maxQuestions);
    }

    public function getMissingRequired(QueryContext $context): array
    {
        $missing = [];
        foreach ($this->required as $field) {
            if ($this->getField($context, $field) === null) {
                $missing[] = $field;
            }
        }
        return $missing;
    }

    public function getMissingRecommended(QueryContext $context): array
    {
        $missing = [];
        foreach ($this->recommended as $field) {
            if ($this->getField($context, $field) === null) {
                $missing[] = $field;
            }
        }
        return $missing;
    }

    public function getNextQuestion(QueryContext $context, int $questionsAsked = 0): ?string
    {
        $missingRequired = $this->getMissingRequired($context);

        if (!empty($missingRequired)) {
            return $this->formatQuestion($missingRequired[0]);
        }

        if ($questionsAsked >= $this->maxQuestionsInRow) {
            return null;
        }

        $missingRecommended = $this->getMissingRecommended($context);
        if (!empty($missingRecommended)) {
            return $this->formatQuestion($missingRecommended[0]);
        }

        return null;
    }

    public function isReadyToSearch(QueryContext $context, int $questionsAsked = 0): bool
    {
        if (!empty($this->getMissingRequired($context))) {
            return false;
        }

        if ($questionsAsked >= $this->maxQuestionsInRow) {
            return true;
        }

        return true;
    }

    public function formatQuestion(string $field): string
    {
        return match ($field) {
            'material' => '¿Qué material vas a grabar/cortar?',
            'job_type' => '¿Qué tipo de trabajo? (grabado, corte, marcado, grabado y corte)',
            'thickness_mm' => '¿Qué espesor tiene el material (en mm)?',
            'machine_power_w' => '¿Cuántos vatios tiene tu láser?',
            'machine' => '¿Qué modelo de máquina usás?',
            'software' => '¿Qué software usás? (LightBurn, LaserGRBL, etc.)',
            default => "¿Podrías darme el dato de {$field}?",
        };
    }

    private function getField(QueryContext $context, string $field): mixed
    {
        return match ($field) {
            'material' => $context->material,
            'thickness_mm' => $context->thicknessMm,
            'machine_power_w' => $context->machinePowerW,
            'job_type' => $context->jobType,
            'machine' => $context->machine,
            'software' => $context->software,
            default => null,
        };
    }

    public function isParameterQuery(string $message): bool
    {
        $parameterKeywords = [
            'parámetro', 'parametro', 'velocidad', 'potencia', 'pasada', 'dpi', 'lpi',
            'frecuencia', 'configuración', 'configuracion', 'ajuste', 'ajustes',
            'qué velocidad', 'que velocidad', 'qué potencia', 'que potencia',
            'cómo configurar', 'como configurar', 'recomendación', 'recomendacion',
            'mejor configuración', 'mejor configuracion', 'valores', 'parámetros',
            'corte', 'grabado', 'marcado', 'hatch', 'speed', 'power', 'pass',
        ];

        $conceptualKeywords = [
            'qué es', 'que es', 'qué significa', 'que significa', 'definición', 'definicion',
            'diferencia', 'diferencias', 'ventaja', 'desventaja', 'pros', 'contras',
            'explicar', 'explicame', 'explicá', 'explica', 'cómo funciona', 'como funciona',
            'para qué sirve', 'para que sirve', 'historia', 'origen', 'teoría', 'teoria',
            'concepto', 'definir', 'significado', 'significa',
        ];

        $lowerMessage = mb_strtolower($message);

        // Check for conceptual keywords first - if found, it's NOT a parameter query
        foreach ($conceptualKeywords as $keyword) {
            if (mb_strpos($lowerMessage, $keyword) !== false) {
                return false;
            }
        }

        // Check for parameter keywords
        foreach ($parameterKeywords as $keyword) {
            if (mb_strpos($lowerMessage, $keyword) !== false) {
                return true;
            }
        }

        // Short queries asking for parameters (like "parámetros para MDF")
        if (mb_strlen($lowerMessage) < 50 && preg_match('/\b(para|en|con|sobre)\b/', $lowerMessage)) {
            return true;
        }

        return false;
    }
}