@props([
    'message' => 'Ups, tuve un problema al consultar la IA. Probá de nuevo en un ratito.',
    'onRetry' => null,
])

<div class="flex justify-start animate-fade-in-up" role="alert">
    <div class="flex gap-2.5 max-w-[85%]">
        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-error-100 dark:bg-error-900/30 flex items-center justify-center">
            <x-icons.info class="w-4 h-4 text-error-600 dark:text-error-400" />
        </div>
        <div class="bg-error-50 dark:bg-error-900/30 border border-error-200 dark:border-error-800 rounded-2xl rounded-tl-md px-4 py-3 flex flex-col gap-2">
            <p class="text-sm text-error-800 dark:text-error-200">{{ $message }}</p>
            @if ($onRetry)
                <button
                    wire:click="{{ $onRetry }}"
                    type="button"
                    class="btn-secondary px-3 py-1.5 text-xs self-start"
                >
                    <x-icons.refresh class="w-3.5 h-3.5" />
                    Reintentar
                </button>
            @endif
        </div>
    </div>
</div>