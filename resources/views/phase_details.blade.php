@props([
    'title_phase' => '',
    'porteur_phase' => '',
    'membres_phase' => '',
    'description_phase' => '',
    'buts_phase' => '',
    'perimetre' => '',
])

<x-app-layout>
    <p>{{ $title_phase }}</p>
    <p>{{ $porteur_phase }}</p>
    <p>{{ $membres_phase }}</p>
    <p>{{ $description_phase }}</p>
    <p>{{ $buts_phase }}</p>
    <p>{{ $buts_phase }}</p>
</x-app-layout>
