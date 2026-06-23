<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet approuvé</title>
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
            background-color: #16a34a;
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
            <h1>Projet approuvé</h1>
        </div>
        <div class="body">
            <p>Cher(e) {{ $name }},</p>

            <p>
                Nous avons le plaisir de vous informer que votre projet a été approuvé par la direction et le secteur.
            </p>

            <p>
                Vous pouvez désormais accéder à la plateforme pour consulter les prochaines étapes et
                commencer la mise en œuvre de votre projet.
            </p>

            <p>
                Si vous avez des questions, n'hésitez pas à contacter votre responsable de projet.
            </p>

            <p>Félicitations et bon courage pour la suite !</p>

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
