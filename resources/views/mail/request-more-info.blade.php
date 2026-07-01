<!DOCTYPE html>
<html lang="fr">
<body style="font-family: sans-serif; color: #374151; padding: 24px;">

    <p>Bonjour {{ $project->proposer->name }},</p>

    <p>
        La direction a examiné votre projet <strong>{{ $project->title }}</strong>
        et souhaite obtenir des informations complémentaires avant de prendre une décision.
    </p>

    <p>
        Cliquez sur le lien ci-dessous pour consulter les annotations et corriger les champs signalés :
    </p>

    <p style="margin: 24px 0;">
        <a
            href="{{ route('projects.revision-form', $project) }}"
            style="background:#4f46e5; color:#fff; padding:12px 24px; border-radius:6px; text-decoration:none; font-weight:600;"
        >
            Corriger ma proposition
        </a>
    </p>

</body>
</html>
