<div 
    x-data="chatWidget()"
    x-init="init()"
    class="fixed bottom-6 right-6 z-50"
    id="chat-widget"
>
    <!-- Floating Button -->
    <button
        @click="toggle()"
        @keydown.enter.prevent="toggle()"
        @keydown.space.prevent="toggle()"
        :aria-expanded="isOpen"
        :aria-label="isOpen ? 'Cerrar asistente láser' : 'Abrir asistente láser'"
        class="relative w-14 h-14 rounded-full bg-gradient-to-br from-laser-500 to-laser-600 shadow-xl flex items-center justify-center transition-all duration-300 hover:shadow-2xl hover:scale-105 focus:outline-none focus-visible:ring-4 focus-visible:ring-laser-500/50 animate-float"
        :class="{ 'rotate-45': isOpen }"
        aria-haspopup="dialog"
        aria-controls="chat-panel"
        type="button"
    >
        <!-- Robot Icon -->
        <div class="absolute inset-0 flex items-center justify-center transition-all duration-300" :class="{ 'opacity-0 rotate-45 scale-50': isOpen, 'opacity-100 rotate-0 scale-100': !isOpen }">
            <x-icons.robot class="w-7 h-7 text-white" />
        </div>
        
        <!-- Close Icon -->
        <div class="absolute inset-0 flex items-center justify-center transition-all duration-300" :class="{ 'opacity-100 rotate-0 scale-100': isOpen, 'opacity-0 -rotate-45 scale-50': !isOpen }">
            <x-icons.close class="w-6 h-6 text-white" />
        </div>
        
        <!-- Notification Badge -->
        <span 
            x-show="unreadCount > 0 && !isOpen"
            x-text="unreadCount"
            class="absolute -top-1 -right-1 w-5 h-5 bg-error-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center animate-pulse-glow"
            aria-label="mensajes sin leer"
        ></span>
        
        <!-- Tooltip -->
        <span 
            class="absolute right-full mr-3 top-1/2 -translate-y-1/2 bg-graphite-900 dark:bg-graphite-100 text-white dark:text-graphite-900 text-sm font-medium px-3 py-2 rounded-lg shadow-lg whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none"
            style="transform-origin: right center;"
        >
            ¿Dudas sobre grabado láser? Preguntame
        </span>
    </button>

    <!-- Chat Panel -->
    <div
        id="chat-panel"
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 transform translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 transform translate-y-4"
        @keydown.escape.window="close()"
        @click.outside="close()"
        class="fixed bottom-16 right-6 w-full max-w-sm lg:max-w-[400px] h-[600px] max-h-[80vh] lg:max-h-[600px] flex flex-col bg-white dark:bg-graphite-900 rounded-2xl shadow-2xl border border-graphite-200 dark:border-graphite-700 overflow-hidden animate-scale-in z-50"
        role="dialog"
        aria-modal="true"
        aria-label="Asistente Láser"
        :class="{ 'fixed inset-4 max-w-full max-h-full rounded-xl lg:rounded-xl bottom-4 right-4': isMobile }"
    >
        <!-- The actual chat component will be rendered here by Livewire -->
        <div x-ref="chatContainer" class="flex-1 flex flex-col min-h-0">
            @livewire('chat.chat-box')
        </div>
    </div>

    <!-- Backdrop for mobile -->
    <div
        x-show="isOpen && isMobile"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 z-[-1] lg:hidden"
        @click="close()"
        aria-hidden="true"
    ></div>
</div>

<script>
    function chatWidget() {
        return {
            isOpen: false,
            unreadCount: 0,
            isMobile: false,
            chatContainer: null,
            previouslyFocusedElement: null,
            
            init() {
                this.checkMobile();
                window.addEventListener('resize', () => this.checkMobile());
                
                // Sync with global Alpine store after Alpine is ready
                this.$nextTick(() => {
                    this.syncWithStore();
                    this.setupStoreWatcher();
                });
                
                // Listen for new messages to increment unread count when closed
                window.addEventListener('livewire:load', () => {
                    Livewire.on('messageReceived', () => {
                        if (!this.isOpen) {
                            this.unreadCount++;
                        }
                    });
                    
                    Livewire.on('chatOpened', () => {
                        this.unreadCount = 0;
                    });
                });
            },
            
            syncWithStore() {
                if (window.Alpine && window.Alpine.store('chatWidget')) {
                    this.isOpen = window.Alpine.store('chatWidget').isOpen;
                }
            },
            
            setupStoreWatcher() {
                // Use polling to watch store changes since $watch doesn't work on stores in Alpine 3
                this.storeWatchInterval = setInterval(() => {
                    if (window.Alpine && window.Alpine.store('chatWidget')) {
                        const storeIsOpen = window.Alpine.store('chatWidget').isOpen;
                        if (storeIsOpen !== this.isOpen) {
                            this.isOpen = storeIsOpen;
                            if (this.isOpen) {
                                this.previouslyFocusedElement = document.activeElement;
                                this.$nextTick(() => {
                                    this.focusInput();
                                });
                            } else if (this.previouslyFocusedElement) {
                                this.previouslyFocusedElement.focus();
                            }
                        }
                    }
                }, 100);
            },
            
            checkMobile() {
                this.isMobile = window.innerWidth < 640;
            },
            
            toggle() {
                if (window.Alpine && window.Alpine.store('chatWidget')) {
                    window.Alpine.store('chatWidget').toggle();
                } else {
                    this.isOpen = !this.isOpen;
                }
            },
            
            open() {
                if (window.Alpine && window.Alpine.store('chatWidget')) {
                    window.Alpine.store('chatWidget').open();
                } else {
                    this.isOpen = true;
                }
            },
            
            close() {
                if (window.Alpine && window.Alpine.store('chatWidget')) {
                    window.Alpine.store('chatWidget').close();
                } else {
                    this.isOpen = false;
                }
                this.unreadCount = 0;
            },
            
            focusInput() {
                const textarea = this.$refs.chatContainer?.querySelector('textarea');
                if (textarea) {
                    textarea.focus();
                }
            }
        };
    }
</script>