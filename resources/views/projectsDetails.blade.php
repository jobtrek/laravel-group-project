@php
    use App\Models\States\RevisionState;
    use App\Models\States\EncoursState;
    use App\Models\States\EvaluationState;


@endphp

<x-app-layout>
    <div class="min-h-screen">
        <div class="w-full max-w-7xl p-4 mx-auto">
            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="p-6">

                    <div class="flex items-start justify-between">
                        <x-project_status :status="$project->status" />
                        <div class="flex items-center gap-3">
                            @if (auth()->id() === $project->proposer_id)
                                @if ($project->status instanceof RevisionState)
                                    <a href="{{ route('projects.revision-form', $project) }}"
                                       class="px-4 py-2 bg-amber-500 text-white rounded-md hover:bg-amber-600 text-sm font-medium transition-colors shadow-sm">
                                        Corriger ma proposition
                                    </a>
                                @endif
                                @if ($project->status->isEditable())
                                    <form action="{{ route('projects.edit', $project) }}" method="GET">
                                        <x-projects.buttons text="Modifier" class="bg-blue-500 text-white p-2"
                                                            type="submit"/>
                                    </form>
                                @endif
                            @endif
                            <x-projects-details.comeBackButton />
                        </div>
                    </div>

                    <h2 class="mt-3 text-2xl font-bold text-gray-900 break-words">
                        {{ $project->title }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 break-words">
                        {{ $project->description }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-4">
                        <x-projects-details.baseInfo name="Proposeur :" :valeur="$project->proposer?->name ?? '—'" />

                        <x-projects-details.baseInfo name="Importance :" :valeur="$project->importance !== null ? number_format($project->importance, 2) : '—'" />

                        <x-projects-details.baseInfo name="Budget :" :valeur="number_format($project->budget_global ?? 0, 2, '.', ' ') . ' CHF'" />

                        <x-projects-details.baseInfo name="Date de création :" :valeur="$project->created_at?->format('d/m/Y') ?? '—'" />
                    </div>

                    <div class="mt-4 flex gap-3">
                        <div class="rounded-lg w-full border border-gray-200 p-3 flex flex-col justify-center">
                            <p class="mb-2 text-sm font-semibold text-gray-800">Avancement</p>
                            <x-progressBar :progress="$project->progress"/>
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                                @if($project->leader)
                                    <x-projects-details.teamUsers :team_name_user="$project->leader->name" :user_status="'Chef de projet'" />
                                @endif
                                @foreach ($project->members as $member)
                                    @if($member->id !== $project->leader_id)
                                        <x-projects-details.teamUsers :team_name_user="$member->name" />
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 rounded-lg border border-gray-200 p-3">
                        <p class="text-sm font-semibold text-gray-800 break-words">Phases :</p>
                        <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            @foreach($project->phases as $phase)
                                <a class="bg-gray-50 p-1 pl-3 pr-3 border rounded-xl hover:bg-gray-100" href="{{ route('phase_details', ['project' => $project, 'phase' => $phase]) }}">
                                    {{ $loop->iteration }} - {{ $phase->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-2 rounded-lg border border-gray-200 p-4">
                        <x-projects-details.graphique :porte="$project->evaluation?->portee_normalized ?? 0" :impact="$project->evaluation?->impact_normalized ?? 0" :confiance="$project->evaluation?->confiance_normalized ?? 0"
                                                      :effort="$project->evaluation?->effort_normalized ?? 0" />
                    </div>

                    @if ($project->status instanceof EncoursState)
                        <div class="mt-4 rounded-lg border border-gray-200 p-3">
                            <p class="text-sm font-semibold text-gray-800">Commentaires</p>

                            <div class="mt-3 space-y-3 overflow-y-auto">
                                @forelse($project->comments->whereNull('field_key') as $comment)
                                    <x-projects-details.Comment_msg :messager_name="$comment->user?->name ?? 'Inconnu'" :commentaire_msg="$comment->content"
                                                                    :date_msg="$comment->created_at?->format('d/m/Y H:i')" />
                                @empty
                                    <span class="mt-1 text-sm text-gray-600">
                                        Actuellement, aucun commentaire n'a été ajouté
                                    </span>
                                @endforelse
                            </div>

                            @can('comment', $project)
                                <form action="{{ route('projects.comments.store', $project) }}" method="POST"
                                      class="mt-3 flex flex-col gap-2">
                                    @csrf
                                    <input type="hidden" name="stage" value="{{ $project->status->getValue() }}">
                                    <textarea name="content" rows="2" required class="w-full rounded-md border border-gray-200 p-2 text-sm"
                                              placeholder="Ajouter un commentaire"></textarea>
                                    <button type="submit"
                                            class="self-end px-4 py-2 bg-blue-700 text-white rounded-md text-sm font-medium">
                                        Commenter
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
