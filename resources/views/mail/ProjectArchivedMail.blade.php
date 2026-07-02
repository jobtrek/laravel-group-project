<x-mail::message>
# Projet archivé

Bonjour {{ $user->name }},

Le projet **{{ $project->title }}** a été archivé automatiquement après une période d'inactivité prolongée. Il se trouvait au stade **{{ ucfirst($project->current_stage) }}** au moment de son archivage.

Si vous pensez qu'il s'agit d'une erreur, vous pouvez consulter le projet dans le frigo et le soumettre à nouveau si nécessaire.

Merci,<br>
{{ config('app.name') }}
</x-mail::message>