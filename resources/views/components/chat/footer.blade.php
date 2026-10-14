@props([
    'input' => '',
    'isLoading' => false,
    'maxLength' => 1000,
])

<div class="border-t border-graphite-200 dark:border-graphite-700 bg-white/90 dark:bg-graphite-900/90 backdrop-blur-sm px-4 py-3 sticky bottom-0">
    <form wire:submit.prevent="send" class="flex items-end gap-2">
        <div class="flex-1 relative min-w-0">
            <textarea
                wire:model.debounce.300ms="input"
                wire:keydown.enter.prevent="send"
                wire:keydown.shift.enter.stop
                placeholder="Escribí tu consulta sobre láser..."
                class="input resize-none min-h-[44px] max-h-[120px] pr-12 py-2.5 text-base"
                @disabled($isLoading)
                aria-label="Tu mensaje"
                rows="1"
                x-ref="textarea"
                @input="this.$refs.textarea.style.height = 'auto'; this.$refs.textarea.style.height = this.$refs.textarea.scrollHeight + 'px';"
            ></textarea>
            
            <div class="absolute bottom-2 right-2 flex items-center gap-1.5">
                <span 
                    x-show="$wire.input.length > {{ $maxLength - 100 }}"
                    x-text="$wire.input.length + ' / {{ $maxLength }}'"
                    class="text-[10px] text-graphite-400 dark:text-graphite-500 bg-graphite-100 dark:bg-graphite-800 px-2 py-0.5 rounded-full font-mono"
                    aria-live="polite"
                ></span>
            </div>
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            :disabled="$wire.isLoading || !$wire.input.trim() || $wire.input.length > {{ $maxLength }}"
            class="btn-primary h-10 w-10 flex items-center justify-center p-0 disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0"
            aria-label="Enviar mensaje"
        >
            <x-icons.send class="w-5 h-5" />
            <span class="sr-only">Enviar</span>
        </button>
    </form>

    <p class="mt-2 text-xs text-graphite-500 dark:text-graphite-400 flex items-center gap-1.5 text-center">
        <x-icons.shield class="w-3.5 h-3.5 text-laser-500 dark:text-laser-400 flex-shrink-0" />
        <span>Respuestas orientativas. Probá siempre en un retazo antes del trabajo final.</span>
    </p>
</div>