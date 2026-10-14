@props([
    'source',
    'experienceIds' => [],
    'webSourceUrls' => [],
    'fallbackLevel' => 0,
    'fallbackReason' => null,
])

<div class="flex flex-wrap items-center gap-1.5">
    @php
        $labels = [
            'community' => ['label' => 'Comunidad', 'icon' => 'users', 'class' => 'badge-community'],
            'external_ai' => ['label' => 'IA Externa', 'icon' => 'cpu', 'class' => 'badge-external-ai'],
            'local_ai' => ['label' => 'IA Local', 'icon' => 'cpu', 'class' => 'badge-local-ai'],
            'external_ai_with_web' => ['label' => 'Fuentes Externas', 'icon' => 'sparkles', 'class' => 'badge-external-ai'],
            'local_ai_with_web' => ['label' => 'Fuentes Externas (Local)', 'icon' => 'sparkles', 'class' => 'badge-local-ai'],
            'question' => ['label' => 'Aclaración', 'icon' => 'info', 'class' => 'badge-warning'],
            'none' => ['label' => 'Sin Info', 'icon' => 'shield', 'class' => 'badge-error'],
        ];
        $data = $labels[$source] ?? ['label' => 'Respuesta', 'icon' => 'info', 'class' => 'badge-info'];
    @endphp

    <span 
        class="{{ $data['class'] }}"
        title="{{ $source === 'community' && !empty($experienceIds) ? 'Basado en experiencias: ' . implode(', ', $experienceIds) : ($source === 'external_ai_with_web' || $source === 'local_ai_with_web') && !empty($webSourceUrls) ? count($webSourceUrls) . ' fuentes web' : '' }}"
    >
        <x-icons.{{ $data['icon'] }} class="w-3 h-3" />
        {{ $data['label'] }}
        @if ($source === 'community' && !empty($experienceIds))
            <span class="ml-1 opacity-70">{{ count($experienceIds) }}</span>
        @endif
    </span>

    @if ($fallbackLevel > 0)
        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs text-graphite-600 dark:text-graphite-400 bg-graphite-100 dark:bg-graphite-800 border border-graphite-200 dark:border-graphite-700">
            <x-icons.info class="w-3 h-3" />
            Fallback {{ $fallbackLevel }}
            @if (!empty($fallbackReason))
                <span class="ml-1 opacity-70">: {{ $fallbackReason }}</span>
            @endif
        </span>
    @endif
</div>