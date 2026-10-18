<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title', 'Tableau de bord') — Admin Élémentium</title>
    <link rel="icon" href="{{ asset('images/logo.webp') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Syne:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
</head>
<body>
@php
    $unread = \App\Models\ContactMessage::whereNull('read_at')->count();
    $pending = \App\Models\Video::where('is_published', false)->count();
    $links = [
        ['admin.dashboard', 'bi-grid-1x2', 'Tableau de bord', null, 'admin.dashboard'],
        ['admin.videos.index', 'bi-camera-reels', 'Vidéos', $pending, 'admin.videos.*'],
        ['admin.portfolio.index', 'bi-images', 'Réalisations', null, 'admin.portfolio.*'],
        ['admin.team.index', 'bi-people', 'Équipe', null, 'admin.team.*'],
        ['admin.messages.index', 'bi-envelope', 'Messages', $unread, 'admin.messages.*'],
        ['admin.subscribers.index', 'bi-send', 'Newsletter', null, 'admin.subscribers.*'],
    ];
@endphp
<div class="admin">
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar__brand"><img src="{{ asset('images/logo.webp') }}" alt=""> Élémentium</a>
        <nav>
            @foreach ($links as [$route, $icon, $label, $count, $pattern])
                <a href="{{ route($route) }}" class="{{ request()->routeIs($pattern) ? 'is-active' : '' }}">
                    <i class="bi {{ $icon }}"></i> {{ $label }}
                    @if ($count)<span class="badge badge--count">{{ $count }}</span>@endif
                </a>
            @endforeach
        </nav>
        <div class="sidebar__bottom">
            <a href="{{ route('home') }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Voir le site</a>
            <form method="post" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit"><i class="bi bi-power"></i> Déconnexion</button>
            </form>
        </div>
    </aside>
    <main class="content">
        @if (session('status'))
            <div class="alert"><i class="bi bi-check-circle"></i> {{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert--error"><i class="bi bi-exclamation-triangle"></i> Merci de corriger les champs indiqués.</div>
        @endif
        @yield('content')
    </main>
</div>
<script>
    // Confirmation avant toute suppression.
    document.addEventListener('submit', (e) => {
        const msg = e.target.dataset.confirm;
        if (msg && !confirm(msg)) e.preventDefault();
    });
</script>
</body>
</html>
