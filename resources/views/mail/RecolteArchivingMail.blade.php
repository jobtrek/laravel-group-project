<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Information sur le project recolte</title>
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
            background-color: #2563eb;
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
        .cta {
            display: inline-block;
            margin-top: 8px;
            padding: 12px 28px;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
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
            <h1>Information sur le project recolte</h1>
        </div>
        <div class="body">
            <p>Cher(e) {{ $user->name }},</p>

            <p>
                Nous vous informons que le projet "{{ $project->title }}" a été archivé après la période de collecte. 
                Cela signifie que le projet est maintenant dans un état final et ne sera plus actif pour la collecte de données.
            </p>

            <p>
                Maintenir votre statut de projet à jour aide toute l'équipe à rester alignée, garantit
                des rapports précis et permet aux responsables de projet d'apporter un soutien là où
                c'est le plus nécessaire.
            </p>

            <p>
                Si votre projet a connu des développements — jalons atteints, tâches accomplies, obstacles
                identifiés ou prochaines étapes planifiées — veuillez prendre un moment pour les consigner
                dès que possible.
            </p>

            <p>
                Si vous avez des questions ou avez besoin d'aide, n'hésitez pas à contacter votre
                responsable de projet ou votre coordinateur d'équipe.
            </p>

            <p>Merci pour vos efforts continus et votre dévouement.</p>

            <p>
                Cordialement,<br>
                <strong>L'équipe de gestion de projets</strong>
            </p>
        </div>
        <div class="footer">
            Ceci est un rappel automatique. Veuillez ne pas répondre directement à cet e-mail.
        </div>
    </div>
</body>
</html>
