@php
    $manifestPath = public_path('build/manifest.json');
    if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        $jsEntry = $manifest['resources/js/app.js']['file'] ?? null;
        if ($jsEntry) {
            echo '<script type="module" src="' . asset('build/' . $jsEntry) . '"></script>';
        }
    }
@endphp