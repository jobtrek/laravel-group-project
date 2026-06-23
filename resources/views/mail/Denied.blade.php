<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet refusé</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .header {
            background-color: #dc2626;
            padding: 32px 40px;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .body {
            padding: 36px 40px;
            color: #374151;
            line-height: 1.7;
            font-size: 15px;
        }
        .body p {
            margin: 0 0 16px;
        }
        .footer {
            padding: 20px 40px;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Projet refusé</h1>
        </div>
        <div class="body">
            <p>Cher(e) {{ $name }},</p>

            <p>
                Nous vous informons que votre projet a été refusé par la direction et le secteur.
            </p>

            <p>
                Nous vous encourageons à revoir votre proposition en tenant compte des retours éventuels
                et à soumettre une nouvelle version si vous le souhaitez.
            </p>

            <p>
                Si vous avez des questions ou souhaitez obtenir plus d'informations sur la décision,
                n'hésitez pas à contacter votre responsable de projet.
            </p>

            <p>
                Cordialement,<br>
                <strong>L'équipe de gestion de projets</strong>
            </p>
        </div>
        <div class="footer">
            Ceci est un message automatique. Veuillez ne pas répondre directement à cet e-mail.
        </div>
    </div>
</body>
</html>
