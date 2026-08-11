<x-mail::message>
# Projets supprimés définitivement

Bonjour {{ $user->name }},

Le job d'archivage automatique a supprimé définitivement {{ count($deletedProjects) }} projet(s) ayant dépassé leur délai de rétention :

<x-mail::table>
| Titre | Stade précédent | Motif |
|:------|:-----------------|:------|
@foreach ($deletedProjects as $project)
| {{ $project['title'] }} | {{ ucfirst($project['stage']) }} | {{ $project['reason'] === 'completed_retention' ? "Complété depuis plus d'un an" : "Archivé depuis plus d'un an" }} |
@endforeach
</x-mail::table>

Cette action est irréversible.

Merci,<br>
{{ config('app.name') }}
</x-mail::message>