<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Comunidad Láser') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-graphite-50 dark:bg-graphite-950 text-graphite-900 dark:text-graphite-50 font-sans antialiased">
    {{ $slot }}
    
    <script>
        // Carga dinámica del módulo Vite JS leyendo el manifest en runtime
        fetch('{{ asset("build/manifest.json") }}')
            .then(res => res.json())
            .then(manifest => {
                const jsEntry = manifest['resources/js/app.js']?.file;
                if (jsEntry) {
                    const script = document.createElement('script');
                    script.type = 'module';
                    script.src = '{{ asset("build/") }}' + jsEntry;
                    document.head.appendChild(script);
                }
            })
            .catch(err => console.warn('Vite JS load failed:', err));
    </script>
    
    @livewireScripts
</body>
</html>