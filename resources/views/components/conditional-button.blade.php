@props([
    'leader_id' => null
    ])

<div>
    @if(filled($leader_id))
    <p>Ajouter un membre</p>
    @else
    <p>Pas de chef de projet ! </p>
    @endif
</div>