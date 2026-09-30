<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hors ligne — DIABA HOTEL</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #F0F0E8;
            color: #101818;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            text-align: center;
            padding: 24px;
        }
        .card { max-width: 380px; }
        .mark {
            width: 64px; height: 64px; margin: 0 auto 20px;
            background: #101818; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
        }
        h1 { font-size: 1.3rem; margin: 0 0 10px; }
        p { color: #6B726D; line-height: 1.5; margin: 0 0 20px; }
        button {
            background: #009C4A; color: #F0F0E8; border: none;
            border-radius: 999px; padding: 10px 22px; font-size: 0.95rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="mark">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="#1DBF63"><path d="M12 2C7 2 3 6 3 11c0 5 4.5 9 9 11 4.5-2 9-6 9-11 0-5-4-9-9-9z"/></svg>
        </div>
        <h1>Pas de connexion</h1>
        <p>Cette page n'est pas disponible hors ligne. Vérifiez votre connexion et réessayez.</p>
        <button onclick="location.reload()">Réessayer</button>
    </div>
</body>
</html>
