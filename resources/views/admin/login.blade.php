<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Connexion — Admin Élémentium</title>
    <link rel="icon" href="{{ asset('images/logo.webp') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Syne:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
</head>
<body class="login">
    <form method="post" action="{{ route('admin.login') }}" class="card form">
        @csrf
        <img src="{{ asset('images/logo.webp') }}" alt="Élémentium Studio">
        <h1>Espace administration</h1>
        <label class="label">E-mail
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')<span class="error">{{ $message }}</span>@enderror
        </label>
        <label class="label">Mot de passe
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        <label class="check"><input type="checkbox" name="remember" value="1"> Rester connecté</label>
        <button type="submit" class="btn btn--primary" style="justify-content:center">Se connecter</button>
        <a href="{{ route('home') }}" class="muted small" style="text-align:center">← Retour au site</a>
    </form>
</body>
</html>
