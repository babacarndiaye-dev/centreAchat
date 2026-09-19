<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hors ligne — Central d'Achat</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #F6F1E4;
            color: #16201B;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            text-align: center;
            padding: 24px;
        }
        .card { max-width: 380px; }
        .mark {
            width: 64px; height: 64px; margin: 0 auto 20px;
            background: #1E4A3D; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
        }
        h1 { font-size: 1.3rem; margin: 0 0 10px; }
        p { color: #5B3A29; line-height: 1.5; margin: 0 0 20px; }
        button {
            background: #1E4A3D; color: #F6F1E4; border: none;
            border-radius: 999px; padding: 10px 22px; font-size: 0.95rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="mark">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="#D6A144"><path d="M12 2C7 2 3 6 3 11c0 5 4.5 9 9 11 4.5-2 9-6 9-11 0-5-4-9-9-9z"/></svg>
        </div>
        <h1>Pas de connexion</h1>
        <p>Cette page n'est pas disponible hors ligne. Vérifiez votre connexion et réessayez.</p>
        <button onclick="location.reload()">Réessayer</button>
    </div>
</body>
</html>
