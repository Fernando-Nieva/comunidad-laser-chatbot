@props([
    'experienceIds',
    'scores' => [],
])

@php
    $showExperiences = false;
@endphp

<div class="relative" x-data="{ open: false }">
    <button
        type="button"
        @click="open = !open"
        @keydown.space.prevent="open = !open"
        @keydown.enter.prevent="open = !open"
        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium text-graphite-600 dark:text-graphite-400 bg-graphite-100 dark:bg-graphite-800 border border-graphite-200 dark:border-graphite-700 hover:bg-graphite-200 dark:hover:bg-graphite-700 transition-colors"
        aria-expanded="false"
        aria-controls="experiences-dropdown"
        aria-label="Ver experiencias de la comunidad"
    >
        <x-icons.users class="w-3.5 h-3.5" />
        Basado en {{ count($experienceIds) }} experiencia{{ count($experienceIds) > 1 ? 's' : '' }}
        <x-icons.chevron-down class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" />
    </button>

    <div
        id="experiences-dropdown"
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 transform"
        x-transition:enter-end="opacity-100 scale-100 transform"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 transform"
        x-transition:leave-end="opacity-0 scale-95 transform"
        @click.outside="open = false"
        class="absolute bottom-full left-0 mb-2 w-72 max-h-60 bg-white dark:bg-graphite-900 border border-graphite-200 dark:border-graphite-700 rounded-xl shadow-lg overflow-hidden z-20 animate-scale-in"
        role="menu"
    >
        <div class="px-3 py-2 bg-graphite-50 dark:bg-graphite-800 border-b border-graphite-200 dark:border-graphite-700">
            <p class="text-xs font-medium text-graphite-700 dark:text-graphite-300">Experiencias utilizadas</p>
        </div>
        <div class="p-2 space-y-2 max-h-48 overflow-y-auto scrollbar-thin">
            @foreach ($experienceIds as $index => $id)
                <div class="px-3 py-2.5 bg-graphite-50/50 dark:bg-graphite-800/50 rounded-lg border border-graphite-100 dark:border-graphite-800 flex items-start gap-2.5" role="menuitem">
                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-laser-100 dark:bg-laser-900/30 text-laser-600 dark:text-laser-400 text-xs font-medium flex items-center justify-center">{{ $index + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-graphite-900 dark:text-graphite-100 truncate">Experiencia #{{ $id }}</p>
                        @if (isset($scores[$index]))
                            <div class="mt-1 flex items-center gap-1.5">
                                <div class="flex-1 h-1.5 bg-graphite-200 dark:bg-graphite-700 rounded-full overflow-hidden">
                                    <div 
                                        class="h-full bg-laser-500 rounded-full transition-all duration-500" 
                                        style="width: {{ min(100, $scores[$index] * 100) }}%"
                                    ></div>
                                </div>
                                <span class="text-[10px] font-mono text-graphite-500 dark:text-graphite-400">{{ number_format($scores[$index] * 100, 1) }}%</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>