<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mot de passe oublié — Back-office</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-body">
<main class="auth-layout">
    <section class="auth-card">
        <div class="auth-brand">
            <div class="auth-brand-icon">TG</div>
            <div><strong>Back-office</strong><span>Système de caisse</span></div>
        </div>
        <div class="auth-heading">
            <h1>Mot de passe oublié</h1>
            <p>Saisissez l’adresse e-mail utilisée pour accéder à votre espace de suivi.</p>
        </div>
        @if ($errors->any())
            <div class="alert alert-error" role="alert">{{ $errors->first() }}</div>
        @endif
        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif
        <form method="post" action="{{ route('password.email') }}" class="auth-form">
            @csrf
            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" placeholder="nom@restaurant.fr" required autofocus>
            </div>
            <button type="submit" class="button auth-submit">Envoyer le lien</button>
        </form>
        <div class="auth-footer-link"><a href="{{ route('login') }}">← Retour à la connexion</a></div>
    </section>
    <footer>Consultation et suivi du système de caisse</footer>
</main>
</body>
</html>
