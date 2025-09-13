<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

<!-- Global Loading Indicator -->
<style>
    .navigation-loading {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #06b6d4);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .navigation-loading.show {
        opacity: 1;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>

<script>
    // Global navigation loading indicator
    document.addEventListener('alpine:navigating', function() {
        const indicator = document.querySelector('.navigation-loading');
        if (indicator) {
            indicator.classList.add('show');
        }
    });
    
    document.addEventListener('alpine:navigated', function() {
        const indicator = document.querySelector('.navigation-loading');
        if (indicator) {
            indicator.classList.remove('show');
        }
    });
</script>
