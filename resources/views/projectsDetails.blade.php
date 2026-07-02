@php
    use App\Models\States\RevisionState;
@endphp


<x-app-layout>
    <div class="w-full max-w-7xl p-4 mx-auto">
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="p-6">

                <div class="flex items-start justify-between">
                    <x-project_status :status="$project->status" />
                    <div class="flex items-center gap-3">
                        @if ($project->status instanceof RevisionState && auth()->id() === $project->proposer_id)
                            <a href="{{ route('projects.revision-form', $project) }}"
                                class="px-4 py-2 bg-amber-500 text-white rounded-md hover:bg-amber-600 text-sm font-medium transition-colors shadow-sm">
                                Corriger ma proposition
                            </a>
                        @endif
                        <x-projects-details.comeBackButton />
                    </div>
                </div>

                <h2 class="mt-3 text-2xl font-bold text-gray-900">
                    {{ $project->title }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $project->description }}
                </p>

                <div class="flex justify-between">

                    <x-projects-details.baseInfo name="Proposeur :" :valeur="$project->proposer?->name ?? '—'" />

                    <x-projects-details.baseInfo name="Importance :" :valeur="$project->importance !== null ? number_format($project->importance, 2) : '—'" />

                    <x-projects-details.baseInfo name="Budget :" :valeur="($project->budget_global ?? 0) . ' CHF'" />

                    <x-projects-details.baseInfo name="Date de creation :" :valeur="$project->created_at?->format('d/m/Y') ?? '—'" />




                </div>

                <div class="mt-4 flex gap-3">

                    <div class="rounded-lg w-full border border-gray-200 p-3 flex flex-col justify-center">
                        <p class="text-sm font-semibold text-gray-800">Avancement</p>

                        <div class="mt-3 h-1.5 w-full rounded-full bg-gray-100">
                            <div class="h-1.5 rounded-full bg-emerald-400" style="width: {{ $project->progress }}%">
                            </div>
                        </div>
                    </div>



                </div>

                <div class="mt-3 grid grid-cols-2 gap-3">

                    <div class="rounded-lg border border-gray-200 p-3 flex flex-col gap-3">
                        <p class="text-sm font-semibold text-gray-800 p-1">Buts</p>
                        @forelse($project->but ?? [] as $but)
                            <x-projects-details.display-buts text_but="{{ $but }}" />
                        @empty
                            <span class="text-sm text-gray-500">Aucun but défini</span>
                        @endforelse
                    </div>

                    <div class="rounded-lg border border-gray-200 p-3">

                        <p class="text-sm font-semibold text-gray-800">Equipe</p>

                        <div>
                            @foreach ($project->members as $member)
                                <x-projects-details.teamUsers :team_name_user="$member->name" :user_status="$member->id === $project->leader_id" />
                            @endforeach
                        </div>

                    </div>

                </div>

                <div class="mt-4 rounded-lg border border-gray-200 p-3">

                    <p class="text-sm font-semibold text-gray-800">Phases :</p>

                    <div class="mt-3 grid grid-cols-4 gap-4">
                        @foreach($project->phases as $phase)
                            <a class="bg-gray-50 p-1  pl-3 pr-3 border rounded-xl hover:bg-gray-100" href="{{ route('phase_details', $phase) }}">
                                {{ $phase->name }}
                            </a>
                        @endforeach
                    </div>

                </div>

                <div class="mt-2 rounded-lg border border-gray-200 p-4">
                    <x-projects-details.graphique :porte="$project->evaluation?->portee_normalized ?? 0" :impact="$project->evaluation?->impact_normalized ?? 0" :confiance="$project->evaluation?->confiance_normalized ?? 0"
                        :effort="$project->evaluation?->effort_normalized ?? 0" />
                </div>

                <div class="mt-4 rounded-lg border border-gray-200 p-3">

                    <p class="text-sm font-semibold text-gray-800">Commentaires</p>

                    <div class="mt-3 space-y-3 overflow-y-auto">

                        @forelse($project->comments as $comment)
                            <x-projects-details.Comment_msg :messager_name="$comment->user?->name ?? 'Unknown'" :commentaire_msg="$comment->content" :date_msg="$comment->created_at?->format('d/m/Y')" />

                        @empty
                            <span class="mt-1 text-sm text-gray-600">
                                Actuellement, aucun commentaire n'a été ajouté
                            </span>
                        @endforelse

                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
