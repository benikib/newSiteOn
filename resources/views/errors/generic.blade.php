<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Une erreur est survenue - BISIKA</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; color: #263238; background: #f4f6f7; font-family: Arial, sans-serif; }
        main { width: min(100%, 520px); }
        h1 { margin: 0 0 .5rem; font-size: 1.5rem; }
        p { color: #5d6870; }
        a { color: #176b87; }
    </style>
</head>
<body>
    <main>
        <h1>{{ $status ?? 500 }}</h1>
        <x-flash-alert :message="$message" type="error" />
        <p>Vous pouvez revenir à la page précédente ou réessayer dans quelques instants.</p>
        <a href="{{ url('/') }}">Retour à l’accueil</a>
    </main>
</body>
</html>