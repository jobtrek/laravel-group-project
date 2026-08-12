@props([
    'leader_id' => null
    ])

    @if(filled($leader_id))
   <a href="{{  route('members.attach') }}" class="pointer-events-auto mt-1 text-sm text-indigo-600 hover:underline">Gérer les membres</a>
    @else
    <a href="{{ route('members.attach') }}" class="pointer-events-auto mt-1 text-sm text-red-600 hover:underline">Pas de chef de projet</a>
    @endif
