<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Back-office</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="app-header">
    <div>
        <strong>Back-office</strong>
        <span>{{ $email }}</span>
    </div>
    <form method="post" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Se déconnecter</button>
    </form>
</header>
<main class="empty-page"></main>
</body>
</html>
