<div 
    class="flex items-center gap-3 px-4 py-3 border-b border-graphite-200 dark:border-graphite-700 bg-white/90 dark:bg-graphite-900/90 backdrop-blur-sm sticky top-0 z-10"
    role="heading"
    aria-level="2"
>
    <div class="relative flex-shrink-0">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-laser-500 to-laser-600 flex items-center justify-center shadow-lg">
            <x-icons.robot class="w-5 h-5 text-white" />
        </div>
        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-success-500 border-2 border-white dark:border-graphite-900 rounded-full animate-pulse-glow" aria-hidden="true"></span>
    </div>
    
    <div class="flex-1 min-w-0">
        <h2 class="font-semibold text-graphite-900 dark:text-white text-base truncate">Asistente Láser</h2>
        <p class="text-xs text-graphite-500 dark:text-graphite-400 truncate">Basado en la comunidad · Respuestas orientativas</p>
    </div>

    <div class="flex items-center gap-1">
        <button
            wire:click="newConversation"
            type="button"
            class="btn-icon text-graphite-500 hover:text-graphite-700 dark:text-graphite-300 dark:hover:text-white"
            aria-label="Nueva conversación"
            title="Nueva conversación"
        >
            <x-icons.refresh class="w-5 h-5" />
        </button>
        
        <button
            wire:click="$dispatch('close-chat-widget')"
            type="button"
            class="btn-icon text-graphite-500 hover:text-graphite-700 dark:text-graphite-300 dark:hover:text-white"
            aria-label="Cerrar chat"
            title="Cerrar"
        >
            <x-icons.close class="w-5 h-5" />
        </button>
    </div>
</div>