<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>@yield('title', config('studio.name').' — '.config('studio.tagline'))</title>
    <meta name="description" content="Élémentium Studio, studio de création audiovisuelle à Ouagadougou : animation 2D/3D, illustration, production audio, jeux vidéo et post-production.">
    <meta property="og:title" content="{{ config('studio.name') }}">
    <meta property="og:description" content="Animation 2D/3D, illustration, production audio et jeux vidéo — Ouagadougou.">
    <meta property="og:image" content="{{ asset('images/hero.webp') }}">
    <meta name="theme-color" content="#060b10">
    <link rel="icon" href="{{ asset('images/logo.webp') }}">

    <script>
        // Thème appliqué avant l'affichage pour éviter un flash.
        try { const t = localStorage.getItem('el-theme'); if (t) document.documentElement.dataset.theme = t; } catch (e) {}
        document.documentElement.classList.add('js');
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Syne:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ filemtime(public_path('css/site.css')) }}">
</head>
<body>
    @yield('content')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js"></script>
    <script type="importmap">
        { "imports": { "three": "https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js" } }
    </script>
    <script src="{{ asset('js/site.js') }}?v={{ filemtime(public_path('js/site.js')) }}"></script>
    <script type="module" src="{{ asset('js/hero3d.js') }}?v={{ filemtime(public_path('js/hero3d.js')) }}"></script>
</body>
</html>
