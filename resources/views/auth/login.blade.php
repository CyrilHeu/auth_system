<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion au back-office</title>
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
            <h1>Connexion</h1>
            <p>Accédez à l’espace de consultation et de suivi de votre restaurant.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ route('login.submit') }}" class="auth-form">
            @csrf
            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input id="email" name="email" type="email" autocomplete="username"
                       value="{{ old('email', $rememberedEmail) }}" placeholder="nom@restaurant.fr" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <div class="auth-options">
                <label><input type="checkbox" name="remember_credentials" value="1" @checked($rememberedEmail !== '')> Se souvenir de moi</label>
                <label><input type="checkbox" name="stay_connected" value="1"> Rester connecté</label>
            </div>
            <div class="auth-secondary-row"><a href="{{ route('password.request') }}">Mot de passe oublié ?</a></div>
            <button type="submit" class="button auth-submit">Se connecter</button>
        </form>
    </section>
    <footer>Consultation et suivi du système de caisse</footer>
</main>
</body>
</html>
