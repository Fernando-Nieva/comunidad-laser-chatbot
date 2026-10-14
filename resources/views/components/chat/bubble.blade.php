@props([
    'message',
    'index',
])

<div 
    class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }} animate-fade-in-up"
    style="animation-delay: {{ $index * 30 }}ms;"
    role="listitem"
    aria-label="{{ $message['role'] === 'user' ? 'Tu mensaje' : 'Respuesta del asistente' }}"
>
    <div class="flex gap-2.5 max-w-[85%] {{ $message['role'] === 'user' ? 'flex-row-reverse' : '' }}">
        <!-- Avatar -->
        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $message['role'] === 'user' ? 'bg-graphite-200 dark:bg-graphite-700' : 'bg-gradient-to-br from-laser-500 to-laser-600' }} {{ $message['role'] === 'user' ? 'order-2' : 'order-1' }}">
            @if ($message['role'] === 'user')
                <x-icons.users class="w-4 h-4 text-graphite-600 dark:text-graphite-300" />
            @else
                <x-icons.robot class="w-4.5 h-4.5 text-white" />
            @endif
        </div>

        <!-- Message Bubble -->
        <div 
            class="flex-1 {{ $message['role'] === 'user' ? 'bg-laser-500 text-white' : 'bg-white dark:bg-graphite-800 text-graphite-900 dark:text-graphite-100 shadow-sm border border-graphite-200 dark:border-graphite-700' }} rounded-2xl px-4 py-3 {{ $message['role'] === 'user' ? 'rounded-tr-md' : 'rounded-tl-md' }}"
        >
            <div class="prose-chat whitespace-pre-wrap">{{ $message['content'] }}</div>
            
            @if ($message['role'] === 'assistant')
                <div class="mt-3 pt-3 border-t {{ $message['role'] === 'user' ? 'border-laser-500/20' : 'border-graphite-200 dark:border-graphite-700' }} flex flex-col gap-2">
                    <!-- Source Badges -->
                    <div class="flex flex-wrap items-center gap-1.5">
                        <x-chat.source-badge 
                            :source="$message['source'] ?? 'local_ai'" 
                            :experience-ids="$message['experience_ids'] ?? []" 
                            :web-source-urls="$message['web_source_urls'] ?? []"
                            :fallback-level="$message['fallback_level'] ?? 0"
                            :fallback-reason="$message['fallback_reason'] ?? null"
                        />
                        
                        @if (!empty($message['scores']))
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs text-graphite-600 dark:text-graphite-400 bg-graphite-100 dark:bg-graphite-800 border border-graphite-200 dark:border-graphite-700">
                                <x-icons.chart class="w-3 h-3" />
                                Scores: {{ implode(', ', array_map(fn($s) => number_format($s, 3), $message['scores'])) }}
                            </span>
                        @endif
                    </div>

                    <!-- Expandable Experiences -->
                    @if (!empty($message['experience_ids']) && $message['source'] === 'community')
                        <x-chat.experiences-dropdown :experience-ids="$message['experience_ids']" :scores="$message['scores'] ?? []" />
                    @endif
                </div>
            @endif

            <!-- Timestamp -->
            <div class="mt-1.5 flex items-center gap-1.5 justify-end text-[10px] text-graphite-400 dark:text-graphite-500">
                <span>{{ now()->format('H:i') }}</span>
                @if ($message['role'] === 'user')
                    <x-icons.check-circle class="w-3 h-3 text-laser-500" />
                @endif
            </div>
        </div>
    </div>
</div>