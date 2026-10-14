<div 
    class="flex flex-col h-full min-h-0 bg-white dark:bg-graphite-900"
    x-data="chatPanel()"
    x-init="init()"
    @keydown.escape.window="$dispatch('close-chat-widget')"
>
    <!-- Header -->
    <x-chat.header />

    <!-- Messages Area -->
    <main 
        class="flex-1 overflow-y-auto p-4 space-y-4 scrollbar-thin" 
        role="log" 
        aria-live="polite" 
        aria-label="Conversación"
        x-ref="messagesContainer"
    >
        @if (empty($messages))
            <!-- Welcome State -->
            <x-chat.welcome />
        @else
            <!-- Messages List -->
            @foreach ($messages as $index => $message)
                @if ($message['role'] === 'assistant' && isset($message['error']))
                    <x-chat.error :message="$message['content']" onRetry="send" />
                @else
                    <x-chat.bubble :message="$message" :index="$index" />
                @endif
            @endforeach
        @endif

        <!-- Typing Indicator -->
        @if ($isLoading)
            <x-chat.typing />
        @endif
    </main>

    <!-- Footer / Input Area -->
    <x-chat.footer :input="$input" :isLoading="$isLoading" :maxLength="1000" />

    <!-- Scroll to bottom button -->
    <button
        x-show="showScrollButton"
        @click="scrollToBottom()"
        class="fixed bottom-20 right-4 z-10 w-10 h-10 rounded-full bg-white dark:bg-graphite-800 shadow-lg border border-graphite-200 dark:border-graphite-700 flex items-center justify-center text-graphite-600 dark:text-graphite-400 hover:bg-graphite-50 dark:hover:bg-graphite-700 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-laser-500"
        aria-label="Ir al último mensaje"
        type="button"
    >
        <x-icons.chevron-down class="w-5 h-5" />
    </button>
