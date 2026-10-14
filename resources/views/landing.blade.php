<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunidad Láser - Asistente IA para Grabado y Corte</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @livewireStyles
    <style>
        * { font-family: 'Inter', system-ui, sans-serif; }
        .gradient-text { background: linear-gradient(135deg, #4f46e5 0%, #a855f7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); }
        .dark .glass { background: rgba(30, 41, 59, 0.8); }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass border-b border-slate-200/50 dark:border-slate-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 019 9.172V5L8 4z" />
                        </svg>
                    </div>
                    <span class="font-semibold text-lg text-slate-900 dark:text-white">Comunidad Láser</span>
                </div>
                <div class="flex items-center gap-3">
                    <a href="/chat" class="hidden sm:block px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                        Entrar al Chat
                    </a>
                    <a href="/chat" class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Probar ahora
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-4xl mx-auto">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200/50 dark:border-indigo-800/50 text-indigo-700 dark:text-indigo-300 text-sm font-medium mb-8">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    Asistente IA especializado en grabado y corte láser
                </div>

                <!-- Headline -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-slate-900 dark:text-white mb-6">
                    Tu experto en <span class="gradient-text">parámetros láser</span>
                </h1>
                
                <!-- Subheadline -->
                <p class="text-lg sm:text-xl text-slate-600 dark:text-slate-300 mb-10 max-w-2xl mx-auto leading-relaxed">
                    Obtén velocidades, potencias y pasadas exactas para tu material y máquina.
                    Basado en experiencias reales de la comunidad + búsqueda web + IA local.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
                    <a href="/chat" class="w-full sm:w-auto px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Abrir Asistente IA
                    </a>
                    <button onclick="document.getElementById('features').scrollIntoView({behavior: 'smooth'})" class="w-full sm:w-auto px-8 py-4 text-base font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all">
                        Ver características
                    </button>
                </div>

                <!-- Trust Indicators -->
                <div class="flex flex-wrap items-center justify-center gap-8 text-sm text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>Respuestas orientativas</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>Cita fuentes</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>100% Local (Ollama)</span>
                    </div>
                </div>
            </div>

            <!-- Floating Cards Preview -->
            <div class="mt-20 relative" id="features">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                    <!-- Card 1 -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Experiencias Reales</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">Base de datos comunitaria con parámetros probados: material, espesor, máquina, potencia, resultado.</p>
                    </div>

                    <!-- Card 2 -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Búsqueda Web Inteligente</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">Si no hay experiencia en la comunidad, busca en la web, filtra instrucciones maliciosas y cita fuentes.</p>
                    </div>

                    <!-- Card 3 -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Privacidad Total</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">Ollama local para embeddings y chat. Tus datos nunca salen de tu servidor. Funciona offline.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Background decoration -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-gradient-to-r from-indigo-500/10 to-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-[400px] h-[400px] bg-gradient-to-bl from-emerald-500/10 to-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </section>

    <!-- How it Works -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white dark:bg-slate-900">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white mb-4">Cómo funciona</h2>
                <p class="text-slate-600 dark:text-slate-300 max-w-2xl mx-auto">Tres niveles de inteligencia, una sola interfaz</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="relative">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-2xl font-bold">1</div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-8 pt-14 border border-slate-200/50 dark:border-slate-700/50">
                        <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">Preguntas a la Comunidad</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-4">Busca en experiencias reales con RAG semántico (embeddings bge-m3 + similitud coseno).</p>
                        <div class="flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>Fuente: comunidad</span>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-12 h-12 rounded-full bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center text-sky-600 dark:text-sky-400 text-2xl font-bold">2</div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-8 pt-14 border border-slate-200/50 dark:border-slate-700/50">
                        <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">Búsqueda Web + IA Externa</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-4">Si no hay datos, busca en la web, limpia inyecciones y pasa contexto a API externa (OpenAI-compatible).</p>
                        <div class="flex items-center gap-2 text-sm text-sky-600 dark:text-sky-400 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            <span>Fuente: web + API externa</span>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-12 h-12 rounded-full bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center text-violet-600 dark:text-violet-400 text-2xl font-bold">3</div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-8 pt-14 border border-slate-200/50 dark:border-slate-700/50">
                        <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">IA Local (Ollama)</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-4">Fallback final: qwen2.5:7b + bge-m3 embeddings 100% local. Funciona sin internet.</p>
                        <div class="flex items-center gap-2 text-sm text-violet-600 dark:text-violet-400 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            <span>Fuente: IA local</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Example Queries -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-50 dark:bg-slate-950">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white mb-4">Ejemplos de consultas</h2>
                <p class="text-slate-600 dark:text-slate-300">Escribe en lenguaje natural, el IA extrae el contexto</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 max-w-5xl mx-auto">
                <a href="/chat" wire:navigate.hover class="group block p-5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/50 dark:border-slate-700/50 hover:border-indigo-300 dark:hover:border-indigo-700/50 hover:shadow-lg transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Material</span>
                    </div>
                    <p class="text-slate-900 dark:text-white font-medium">"¿Velocidad para MDF 3mm?"</p>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Detecta: material=MDF, espesor=3mm</p>
                </a>

                <a href="/chat" wire:navigate.hover class="group block p-5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/50 dark:border-slate-700/50 hover:border-indigo-300 dark:hover:border-indigo-700/50 hover:shadow-lg transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Máquina</span>
                    </div>
                    <p class="text-slate-900 dark:text-white font-medium">"Tengo Sculpfun S9 10W"</p>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Guarda contexto multi-turno</p>
                </a>

                <a href="/chat" wire:navigate.hover class="group block p-5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/50 dark:border-slate-700/50 hover:border-indigo-300 dark:hover:border-indigo-700/50 hover:shadow-lg transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Seguridad</span>
                    </div>
                    <p class="text-slate-900 dark:text-white font-medium">"¿Puedo cortar PVC?"</p>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Alerta humos tóxicos + ventilación</p>
                </a>

                <a href="/chat" wire:navigate.hover class="group block p-5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/50 dark:border-slate-700/50 hover:border-indigo-300 dark:hover:border-indigo-700/50 hover:shadow-lg transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Conceptos</span>
                    </div>
                    <p class="text-slate-900 dark:text-white font-medium">"Diferencia vector vs raster"</p>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Explicaciones técnicas claras</p>
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-3xl p-10 sm:p-16 shadow-2xl relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zm30 0V0h-2v4h-4v2h4v4h2V6h4V4zm0 30v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"%3E%3C/path%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">¿Listo para probar?</h2>
                    <p class="text-indigo-100 text-lg mb-8 max-w-lg mx-auto">Haz tu primera consulta en segundos. Sin registro, sin API keys, 100% local.</p>
                    <a href="/chat" class="inline-flex items-center gap-2 px-8 py-4 text-base font-semibold text-indigo-600 bg-white rounded-xl hover:bg-slate-50 shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Empezar ahora
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-400 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 019 9.172V5L8 4z" />
                            </svg>
                        </div>
                        <span class="font-semibold text-lg text-white">Comunidad Láser</span>
                    </div>
                    <p class="text-slate-500 text-sm max-w-xs">Asistente IA para grabado y corte láser. Experiencias reales, búsqueda web, IA local. Hecho para makers.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">Enlaces</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/chat" class="hover:text-white transition-colors">Chat IA</a></li>
                        <li><a href="/health" class="hover:text-white transition-colors">Health Check</a></li>
                        <li><a href="/debug" class="hover:text-white transition-colors">Debug</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">Configuración</h4>
                    <ul class="space-y-2 text-sm">
                        <li><code class="text-slate-500">OLLAMA_BASE_URL</code></li>
                        <li><code class="text-slate-500">OLLAMA_CHAT_MODEL</code></li>
                        <li><code class="text-slate-500">SEARCH_API_KEY</code></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-sm text-slate-500">⚠️ Las respuestas son orientativas. Probá siempre en retazo antes del trabajo final.</p>
                <p class="text-xs text-slate-600 dark:text-slate-500">v{{ config('app.version', '1.0.0') }} · Laravel 13 · Livewire 3</p>
            </div>
        </div>
    </footer>
</body>
</html>