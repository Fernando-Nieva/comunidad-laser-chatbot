<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Comunidad Láser') }} - Asistente IA para Grabado Láser</title>
    <meta name="description" content="Comunidad Láser: Plataforma colaborativa para compartir experiencias de grabado láser. Preguntale al asistente IA sobre parámetros, materiales, máquinas y más.">
    
    <!-- Define chatPanel globally BEFORE any modules load -->
    <script>
        window.chatPanel = function() {
            const self = {
                messagesContainer: null,
                showScrollButton: false,
                userHasScrolled: false,
                
                init() {
                    this.messagesContainer = this.$refs.messagesContainer;
                    this.scrollToBottom({ behavior: 'instant' });
                    
                    this.messagesContainer?.addEventListener('scroll', function() {
                        self.checkScrollPosition();
                    }, { passive: true });
                    
                    const observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                                mutation.addedNodes.forEach(function(node) {
                                    if (node.nodeType === 1 && node.hasAttribute('role') && node.getAttribute('role') === 'listitem') {
                                        if (!self.userHasScrolled) {
                                            self.scrollToBottom();
                                        }
                                    }
                                });
                            }
                        });
                    }.bind(self));
                    
                    if (self.messagesContainer) {
                        observer.observe(self.messagesContainer, { childList: true, subtree: true });
                    }
                    
                    setTimeout(function() {
                        if (self.$watch) {
                            self.$watch('$wire.messages', function() {
                                self.$nextTick(function() {
                                    if (!self.userHasScrolled) {
                                        self.scrollToBottom();
                                    }
                                    self.checkScrollPosition();
                                });
                            });
                        }
                    }.bind(self), 0);
                },
                
                checkScrollPosition() {
                    if (!self.messagesContainer) return;
                    
                    const { scrollTop, scrollHeight, clientHeight } = self.messagesContainer;
                    const isAtBottom = scrollHeight - scrollTop - clientHeight < 100;
                    
                    self.showScrollButton = !isAtBottom && scrollTop > 200;
                    self.userHasScrolled = !isAtBottom;
                },
                
                scrollToBottom(options = { behavior: 'smooth' }) {
                    if (self.messagesContainer) {
                        self.messagesContainer.scrollTo({
                            top: self.messagesContainer.scrollHeight,
                            ...options
                        });
                        self.userHasScrolled = false;
                    }
                }
            };
            
            return self;
        };
    </script>
    
    <script>
        // Register Alpine store - works whether Alpine is already loaded or not
        function registerChatWidgetStore() {
            if (window.Alpine) {
                window.Alpine.store('chatWidget', {
                    isOpen: false,
                    wasOpen: false,
                    
                    init() {
                        const saved = sessionStorage.getItem('chatWidgetOpen');
                        if (saved === 'true') {
                            this.isOpen = true;
                            this.wasOpen = true;
                        }
                        
                        window.addEventListener('open-chat-widget', () => this.open());
                        window.addEventListener('close-chat-widget', () => this.close());
                    },
                    
                    open() {
                        this.isOpen = true;
                    },
                    
                    close() {
                        this.isOpen = false;
                    },
                    
                    toggle() {
                        this.isOpen = !this.isOpen;
                    }
                });
            }
        }
        
        // Try to register immediately if Alpine is already available
        if (window.Alpine) {
            registerChatWidgetStore();
        }
        
        // Also listen for alpine:init in case Alpine loads later
        document.addEventListener('alpine:init', registerChatWidgetStore);
    </script>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Comunidad Láser') }} - Asistente IA para Grabado Láser</title>
    <meta name="description" content="Comunidad Láser: Plataforma colaborativa para compartir experiencias de grabado láser. Preguntale al asistente IA sobre parámetros, materiales, máquinas y más.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-graphite-50 dark:bg-graphite-950 text-graphite-900 dark:text-graphite-50 font-sans antialiased">
    <div id="app" class="h-full flex flex-col">
        <!-- Header/Nav -->
        <header class="fixed top-0 left-0 right-0 z-40 bg-white/80 dark:bg-graphite-950/80 backdrop-blur-md border-b border-graphite-200 dark:border-graphite-800">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Navegación principal">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-laser-500 to-laser-600 flex items-center justify-center shadow-lg">
                            <x-icons.robot class="w-5 h-5 text-white" />
                        </div>
                        <span class="font-display text-xl font-bold text-graphite-900 dark:text-graphite-100">Comunidad Láser</span>
                    </div>
                    <div class="hidden md:flex items-center gap-6">
                        <a href="#como-funciona" class="text-sm font-medium text-graphite-600 dark:text-graphite-400 hover:text-laser-600 dark:hover:text-laser-400 transition-colors">Cómo funciona</a>
                        <a href="#ejemplos" class="text-sm font-medium text-graphite-600 dark:text-graphite-400 hover:text-laser-600 dark:hover:text-laser-400 transition-colors">Ejemplos</a>
                        <button
                            @click="$dispatch('open-chat-widget')"
                            class="btn-primary hidden sm:inline-flex"
                            aria-label="Abrir asistente láser"
                        >
                            <x-icons.message-square class="w-4 h-4" />
                            Preguntar al asistente
                        </button>
                    </div>
                    <button
                        @click="$dispatch('open-chat-widget')"
                        class="md:hidden btn-icon text-graphite-600 hover:text-laser-600 dark:text-graphite-400 dark:hover:text-laser-400"
                        aria-label="Abrir asistente láser"
                    >
                        <x-icons.message-square class="w-5 h-5" />
                    </button>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col pt-16">
            <!-- Hero Section -->
            <section class="relative flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-20 lg:py-32 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-laser-50/50 via-transparent to-cyan-laser-50/50 dark:from-laser-900/20 dark:via-transparent dark:to-cyan-laser-900/20" aria-hidden="true"></div>
                <div class="absolute top-20 left-10 w-72 h-72 bg-laser-500/10 rounded-full blur-3xl animate-float" aria-hidden="true"></div>
                <div class="absolute bottom-20 right-10 w-96 h-96 bg-cyan-laser-500/10 rounded-full blur-3xl animate-float" style="animation-delay: 1.5s;" aria-hidden="true"></div>
                
                <div class="relative max-w-4xl mx-auto text-center">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 dark:bg-graphite-900/80 backdrop-blur-sm border border-graphite-200 dark:border-graphite-700 mb-8 animate-fade-in-up">
                        <span class="w-2 h-2 bg-success-500 rounded-full animate-pulse" aria-hidden="true"></span>
                        <span class="text-sm font-medium text-graphite-700 dark:text-graphite-300">Beta pública · Respuestas basadas en experiencias reales</span>
                    </div>
                    
                    <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold text-graphite-900 dark:text-graphite-50 leading-tight mb-6 animate-fade-in-up delay-1 text-balance">
                        Tu asistente experto en
                        <span class="block bg-gradient-to-r from-laser-500 via-laser-600 to-cyan-laser-500 bg-clip-text text-transparent">grabado y corte láser</span>
                    </h1>
                    
                    <p class="text-lg sm:text-xl text-graphite-600 dark:text-graphite-400 mb-10 max-w-2xl mx-auto animate-fade-in-up delay-2 leading-relaxed">
                        Preguntá parámetros, materiales, máquinas, software y seguridad. 
                        La IA consulta primero las experiencias de la comunidad y complementa con conocimiento técnico.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up delay-3">
                        <button
                            @click="$dispatch('open-chat-widget')"
                            class="btn-primary px-8 py-3.5 text-base w-full sm:w-auto group"
                            aria-label="Abrir chat del asistente láser"
                        >
                            <x-icons.message-square class="w-5 h-5" />
                            Preguntarle al asistente
                            <span class="hidden group-hover:inline-block transition-all" aria-hidden="true">→</span>
                        </button>
                        <a
                            href="#como-funciona"
                            class="btn-secondary px-8 py-3.5 text-base w-full sm:w-auto"
                        >
                            Cómo funciona
                        </a>
                    </div>

                    <div class="mt-16 animate-fade-in-up delay-4">
                        <div class="flex flex-wrap items-center justify-center gap-8 text-sm text-graphite-500 dark:text-graphite-500">
                            <div class="flex items-center gap-2">
                                <x-icons.check-circle class="w-5 h-5 text-success-500" />
                                <span>Basado en experiencias reales</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-icons.check-circle class="w-5 h-5 text-success-500" />
                                <span>Respuestas siempre orientativas</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-icons.check-circle class="w-5 h-5 text-success-500" />
                                <span>Privacidad primero</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- How It Works Section -->
            <section id="como-funciona" class="py-20 lg:py-28 px-4 sm:px-6 lg:px-8 bg-white dark:bg-graphite-900">
                <div class="max-w-7xl mx-auto">
                    <header class="text-center mb-16">
                        <h2 class="font-display text-3xl sm:text-4xl font-bold text-graphite-900 dark:text-graphite-100 mb-4">Cómo funciona</h2>
                        <p class="text-lg text-graphite-600 dark:text-graphite-400 max-w-2xl mx-auto">
                            Tres pasos simples para obtener la mejor orientación para tus proyectos láser
                        </p>
                    </header>

                    <div class="grid md:grid-cols-3 gap-6 lg:gap-8">
                        <article class="relative group">
                            <div class="absolute -top-3 -right-3 w-16 h-16 bg-laser-100 dark:bg-laser-900/30 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300" aria-hidden="true"></div>
                            <div class="card p-6 h-full relative z-10 border-l-4 border-laser-500 hover:shadow-lg transition-shadow duration-300">
                                <div class="w-12 h-12 rounded-xl bg-laser-100 dark:bg-laser-900/30 flex items-center justify-center mb-4">
                                    <span class="font-display text-2xl font-bold text-laser-600 dark:text-laser-400">1</span>
                                </div>
                                <h3 class="text-lg font-semibold text-graphite-900 dark:text-graphite-100 mb-2">Comunidad primero</h3>
                                <p class="text-graphite-600 dark:text-graphite-400 mb-4">
                                    Buscamos en miles de experiencias reales compartidas por makers como vos: mismo material, máquina, espesor y tipo de trabajo.
                                </p>
                                <div class="flex items-center gap-2 text-sm text-graphite-500 dark:text-graphite-500">
                                    <x-icons.users class="w-5 h-5 text-laser-500" />
                                    <span>Experiencias verificadas</span>
                                </div>
                            </div>
                        </article>

                        <article class="relative group">
                            <div class="absolute -top-3 -right-3 w-16 h-16 bg-cyan-laser-100 dark:bg-cyan-laser-900/30 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300" aria-hidden="true"></div>
                            <div class="card p-6 h-full relative z-10 border-l-4 border-cyan-laser-500 hover:shadow-lg transition-shadow duration-300">
                                <div class="w-12 h-12 rounded-xl bg-cyan-laser-100 dark:bg-cyan-laser-900/30 flex items-center justify-center mb-4">
                                    <span class="font-display text-2xl font-bold text-cyan-laser-600 dark:text-cyan-laser-400">2</span>
                                </div>
                                <h3 class="text-lg font-semibold text-graphite-900 dark:text-graphite-100 mb-2">IA cuando falta info</h3>
                                <p class="text-graphite-600 dark:text-graphite-400 mb-4">
                                    Si no hay experiencias suficientes, la IA complementa con conocimiento técnico general (nunca inventa parámetros).
                                </p>
                                <div class="flex items-center gap-2 text-sm text-graphite-500 dark:text-graphite-500">
                                    <x-icons.cpu class="w-5 h-5 text-cyan-laser-500" />
                                    <span>Conocimiento técnico validado</span>
                                </div>
                            </div>
                        </article>

                        <article class="relative group">
                            <div class="absolute -top-3 -right-3 w-16 h-16 bg-warning-100 dark:bg-warning-900/30 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300" aria-hidden="true"></div>
                            <div class="card p-6 h-full relative z-10 border-l-4 border-warning-500 hover:shadow-lg transition-shadow duration-300">
                                <div class="w-12 h-12 rounded-xl bg-warning-100 dark:bg-warning-900/30 flex items-center justify-center mb-4">
                                    <span class="font-display text-2xl font-bold text-warning-600 dark:text-warning-400">3</span>
                                </div>
                                <h3 class="text-lg font-semibold text-graphite-900 dark:text-graphite-100 mb-2">Respuestas orientativas</h3>
                                <p class="text-graphite-600 dark:text-graphite-400 mb-4">
                                    Siempre rangos y referencias, nunca valores garantizados. Cada respuesta indica su fuente y nivel de confianza.
                                </p>
                                <div class="flex items-center gap-2 text-sm text-graphite-500 dark:text-graphite-500">
                                    <x-icons.shield class="w-5 h-5 text-warning-500" />
                                    <span>Probá en retazo siempre</span>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <!-- Examples Section -->
            <section id="ejemplos" class="py-20 lg:py-28 px-4 sm:px-6 lg:px-8 bg-graphite-50 dark:bg-graphite-950">
                <div class="max-w-7xl mx-auto">
                    <header class="text-center mb-16">
                        <h2 class="font-display text-3xl sm:text-4xl font-bold text-graphite-900 dark:text-graphite-100 mb-4">Ejemplos de consultas</h2>
                        <p class="text-lg text-graphite-600 dark:text-graphite-400 max-w-2xl mx-auto">
                            Hacé clic en cualquier ejemplo para abrir el chat con la pregunta precargada
                        </p>
                    </header>

                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @php
                            $examples = [
                                ['icon' => 'sliders-horizontal', 'title' => 'Parámetros MDF 3mm', 'query' => 'Velocidad y potencia para MDF 3mm con láser diodo 10W'],
                                ['icon' => 'shield', 'title' => 'Materiales peligrosos', 'query' => 'Qué materiales NO debo cortar ni grabar con láser por seguridad'],
                                ['icon' => 'cpu', 'title' => 'Mi máquina: Sculpfun S9', 'query' => 'Tengo un Sculpfun S9 10W, qué materiales puedo grabar y parámetros recomendados'],
                                ['icon' => 'beaker', 'title' => 'Vectorial vs Raster', 'query' => 'Diferencia entre grabado vectorial y raster, cuándo usar cada uno'],
                                ['icon' => 'cube', 'title' => 'Grabar acrílico', 'query' => 'Parámetros para grabado en acrílico transparente 5mm, evitar burbujas'],
                                ['icon' => 'flame', 'title' => 'Corte madera contrachapada', 'query' => 'Cómo cortar madera contrachapada 4mm sin quemar bordes, air assist sí/no'],
                                ['icon' => 'sparkles', 'title' => 'Grabar cuero', 'query' => 'Técnicas y parámetros para grabar cuero natural y sintético'],
                                ['icon' => 'wrench', 'title' => 'Mantenimiento láser', 'query' => 'Cada cuánto limpiar lentes y espejos, qué productos usar'],
                            ];
                        @endphp

                        @foreach ($examples as $index => $example)
                            <button
                                wire:click="$set('input', '{{ addslashes($example['query']) }}')"
                                wire:click="$dispatch('open-chat-widget')"
                                type="button"
                                class="card p-5 hover:shadow-xl hover:border-laser-300 dark:hover:border-laser-700 transition-all duration-300 group animate-fade-in-up text-left"
                                style="animation-delay: {{ $index * 50 }}ms;"
                                aria-label="Preguntar: {{ $example['title'] }}"
                            >
                                <div class="w-11 h-11 rounded-xl bg-laser-100 dark:bg-laser-900/30 flex items-center justify-center mb-4 group-hover:bg-laser-500 group-hover:text-white dark:group-hover:bg-laser-500 transition-colors">
                                    <x-icons.{{ $example['icon'] }} class="w-5 h-5 text-laser-600 dark:text-laser-400 group-hover:text-white transition-colors" />
                                </div>
                                <h4 class="font-medium text-graphite-900 dark:text-graphite-100 mb-1">{{ $example['title'] }}</h4>
                                <p class="text-sm text-graphite-500 dark:text-graphite-400 line-clamp-2">{{ $example['query'] }}</p>
                            </button>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="py-20 lg:py-28 px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl mx-auto text-center">
                    <div class="card p-8 lg:p-12 bg-gradient-to-br from-laser-500 to-laser-600 rounded-3xl relative overflow-hidden">
                        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.03\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zm0 30v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 36v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 6V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50" aria-hidden="true"></div>
                        <div class="relative z-10">
                            <h2 class="font-display text-3xl sm:text-4xl font-bold text-white mb-4">¿Listo para probar?</h2>
                            <p class="text-laser-100 mb-8 max-w-lg mx-auto">Abrí el asistente y hacé tu primera consulta. La comunidad y la IA te esperan.</p>
                            <button
                                @click="$dispatch('open-chat-widget')"
                                class="btn bg-white text-laser-600 hover:bg-graphite-100 px-8 py-3.5 text-base font-semibold"
                                aria-label="Abrir asistente láser"
                            >
                                <x-icons.message-square class="w-5 h-5" />
                                Empezar a chatear
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-graphite-900 border-t border-graphite-200 dark:border-graphite-800 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="grid md:grid-cols-3 gap-8 mb-8">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-laser-500 to-laser-600 flex items-center justify-center">
                                <x-icons.robot class="w-5 h-5 text-white" />
                            </div>
                            <span class="font-display text-xl font-bold text-graphite-900 dark:text-graphite-100">Comunidad Láser</span>
                        </div>
                        <p class="text-graphite-600 dark:text-graphite-400 text-sm leading-relaxed">
                            Plataforma colaborativa donde makers comparten experiencias reales de grabado, corte y marcado láser. La IA aprende de la comunidad para darte la mejor orientación.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-graphite-900 dark:text-graphite-100 mb-4">Recursos</h4>
                        <ul class="space-y-2 text-sm text-graphite-600 dark:text-graphite-400">
                            <li><a href="#" class="hover:text-laser-600 dark:hover:text-laser-400 transition-colors">Guía de materiales</a></li>
                            <li><a href="#" class="hover:text-laser-600 dark:hover:text-laser-400 transition-colors">Base de experiencias</a></li>
                            <li><a href="#" class="hover:text-laser-600 dark:hover:text-laser-400 transition-colors">Seguridad láser</a></li>
                            <li><a href="#" class="hover:text-laser-600 dark:hover:text-laser-400 transition-colors">Software recomendado</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-graphite-900 dark:text-graphite-100 mb-4">Legal</h4>
                        <ul class="space-y-2 text-sm text-graphite-600 dark:text-graphite-400">
                            <li><a href="#" class="hover:text-laser-600 dark:hover:text-laser-400 transition-colors">Términos de uso</a></li>
                            <li><a href="#" class="hover:text-laser-600 dark:hover:text-laser-400 transition-colors">Política de privacidad</a></li>
                            <li><a href="#" class="hover:text-laser-600 dark:hover:text-laser-400 transition-colors">Aviso de responsabilidad</a></li>
                        </ul>
                    </div>
                </div>
                <div class="pt-8 border-t border-graphite-200 dark:border-graphite-800 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-graphite-500 dark:text-graphite-500">
                        © {{ date('Y') }} Comunidad Láser. Todas las respuestas son orientativas.
                    </p>
                    <p class="text-xs text-graphite-400 dark:text-graphite-500 flex items-center gap-1.5">
                        <x-icons.shield class="w-3.5 h-3.5 text-laser-500" />
                        Probá siempre en un retazo antes del trabajo final.
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Chat Widget -->
    <x-chat.widget />

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.addEventListener('keydown', (e) => {
                if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                    e.preventDefault();
                    window.dispatchEvent(new CustomEvent('open-chat-widget'));
                }
                if (e.key === 'Escape') {
                    window.dispatchEvent(new CustomEvent('close-chat-widget'));
                }
            });
        });
    </script>
</body>
</html>