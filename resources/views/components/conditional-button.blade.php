@props([
    'leader_id' => null
    ])

    @if(filled($leader_id))
   <a href="#" class="mt-1 text-sm text-indigo-600 hover:underline">+ Ajouter un membre</a>
    @else
    <a href="#" class="mt-1 text-sm text-red-600 hover:underline">Pas de chef de projet</a>
    @endif
