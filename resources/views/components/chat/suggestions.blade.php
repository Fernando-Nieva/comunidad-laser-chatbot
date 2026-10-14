@props([
    'suggestions' => [
        'Parámetros para MDF 3 mm con láser de 10W',
        '¿Qué es el DPI/LPI?',
        'Cómo grabar cuero',
        'Diferencia entre láser diodo y CO2',
    ],
])

<div class="flex flex-col gap-3" role="list" aria-label="Preguntas sugeridas">
    @foreach ($suggestions as $index => $suggestion)
        <button
            wire:click="$set('input', '{{ addslashes($suggestion) }}')"
            wire:keydown.enter="$set('input', '{{ addslashes($suggestion) }}')"
            type="button"
            class="text-left px-4 py-3 bg-white dark:bg-graphite-800 border border-graphite-200 dark:border-graphite-700 rounded-xl hover:border-laser-300 dark:hover:border-laser-700 hover:shadow-md transition-all duration-200 group animate-fade-in-up"
            style="animation-delay: {{ 100 + $index * 50 }}ms;"
            role="listitem"
            aria-label="{{ $suggestion }}"
        >
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-laser-50 dark:bg-laser-900/30 flex items-center justify-center">
                    <x-icons.message-square class="w-4 h-4 text-laser-600 dark:text-laser-400" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-graphite-900 dark:text-graphite-100 group-hover:text-laser-600 dark:group-hover:text-laser-400 transition-colors">{{ $suggestion }}</p>
                    <p class="text-xs text-graphite-400 dark:text-graphite-500 mt-0.5">Preguntar al asistente</p>
                </div>
                <x-icons.chevron-left class="w-4 h-4 text-graphite-300 dark:text-graphite-600 group-hover:text-laser-500 transition-colors" />
            </div>
        </button>
    @endforeach
</div>