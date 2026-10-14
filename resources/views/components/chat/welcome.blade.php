@props([
    'suggestions' => [
        'Parámetros para MDF 3 mm con láser de 10W',
        '¿Qué es el DPI/LPI?',
        'Cómo grabar cuero',
        'Diferencia entre láser diodo y CO2',
    ],
])

<div class="flex flex-col items-center justify-center min-h-[300px] px-4 text-center animate-fade-in-up">
    <div class="relative mb-6">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-laser-500 to-laser-600 flex items-center justify-center shadow-xl mx-auto">
            <x-icons.robot class="w-8 h-8 text-white" />
        </div>
        <div class="absolute -bottom-2 -right-2 w-5 h-5 bg-success-500 border-3 border-white dark:border-graphite-900 rounded-full animate-pulse-glow" aria-hidden="true"></div>
    </div>
    
    <h3 class="font-display text-xl font-semibold text-graphite-900 dark:text-graphite-100 mb-2">¡Hola! Soy tu asistente láser</h3>
    <p class="text-graphite-600 dark:text-graphite-400 mb-6 max-w-xs leading-relaxed">
        Preguntame lo que necesites sobre <strong>grabado, corte o marcado láser</strong>: materiales, máquinas, parámetros, software, seguridad...
    </p>
    
    <x-chat.suggestions :suggestions="$suggestions" />
</div>