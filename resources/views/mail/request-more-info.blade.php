<!DOCTYPE html>
<html lang="fr">
<body style="font-family: sans-serif; color: #374151; padding: 24px;">

    <p>Bonjour {{ $project->proposer->name }},</p>

    <p>
        La direction a examiné votre projet <strong>{{ $project->title }}</strong>
        et souhaite obtenir des informations complémentaires avant de prendre une décision.
    </p>

    <p>
        Cliquez sur le lien ci-dessous pour consulter les annotations et mettre à jour votre proposition :
    </p>

    <p style="margin: 24px 0;">
        
            href="{{ route('propositions') }}"
            style="background:#4f46e5; color:#fff; padding:12px 24px; border-radius:6px; text-decoration:none; font-weight:600;"
        >
            Voir ma proposition
        </a>
    </p>

    {{--
        TODO: replace route('propositions') with route('projects.revision-form', $project)
        once the proposer revision page is built.
    --}}

        <a
            href="{{ route('propositions') }}"
            style="background:#4f46e5; color:#fff; padding:12px 24px; border-radius:6px; text-decoration:none; font-weight:600;"
        >
            Voir ma proposition
        </a>

</body>
</html>