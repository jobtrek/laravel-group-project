@props([
    'users' => [],
    'myProposals' => false,
    'showProposer' => true,
])
<div class="w-full">
    <form method="GET" action="{{ request()->url() }}"
          class="bg-slate-800 border border-slate-700 rounded-2xl shadow-sm p-4 w-full flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:items-end">
        <div class="flex flex-col gap-1 min-w-[180px]">
            <label for="sort" class="text-xs font-medium text-white">Trier par</label>
            <select name="sort" id="sort"
                    class="border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 bg-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Par défaut</option>
                <option value="az" {{ request('sort') === 'az'              ? 'selected' : '' }}>A → Z</option>
                <option value="za" {{ request('sort') === 'za'              ? 'selected' : '' }}>Z → A</option>
                <option value="recent" {{ request('sort') === 'recent'          ? 'selected' : '' }}>Plus récent
                </option>
                <option value="oldest" {{ request('sort') === 'oldest'          ? 'selected' : '' }}>Plus ancien
                </option>
                <option value="importance_desc" {{ request('sort') === 'importance_desc' ? 'selected' : '' }}>Importance
                    haute → basse
                </option>
                <option value="importance_asc" {{ request('sort') === 'importance_asc'  ? 'selected' : '' }}>Importance
                    basse → haute
                </option>
            </select>
        </div>
        <div class="flex flex-col gap-1 min-w-[110px]">
            <label for="score_min" class="text-xs font-medium text-white">Score min</label>
            <input type="number" name="score_min" id="score_min"
                   min="0" max="250" step="1"
                   value="{{ request('score_min') }}"
                   placeholder="ex. 60"
                   class="border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 bg-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>
        <div class="flex flex-col gap-1 min-w-[140px]">
            <label for="date_from" class="text-xs font-medium text-white">Du</label>
            <input type="date" name="date_from" id="date_from"
                   value="{{ request('date_from') }}"
                   class="border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 bg-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>
        <div class="flex flex-col gap-1 min-w-[140px]">
            <label for="date_to" class="text-xs font-medium text-white">Au</label>
            <input type="date" name="date_to" id="date_to"
                   value="{{ request('date_to') }}"
                   class="border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 bg-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>
        @if($myProposals)
            <div class="flex flex-col gap-1 min-w-[160px]">
                <label for="proposer_id" class="text-xs font-medium text-white">Proposeur</label>
                <select name="proposer_id" id="proposer_id"
                        class="border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 bg-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="" {{ request('proposer_id') !== 'all' ? 'selected' : '' }}>Mes projets</option>
                    <option value="all" {{ request('proposer_id') === 'all' ? 'selected' : '' }}>Tous les proposeurs
                    </option>
                </select>
            </div>
        @elseif($showProposer && count($users))
            <div class="flex flex-col gap-1 min-w-[160px]">
                <label for="proposer_id" class="text-xs font-medium text-white">Proposeur</label>
                <select name="proposer_id" id="proposer_id"
                        class="border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 bg-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="" {{ request('proposer_id') !== 'mine' ? 'selected' : '' }}>Tous les proposeurs
                    </option>
                    <option value="mine" {{ request('proposer_id') === 'mine' ? 'selected' : '' }}>Mes projets</option>
                </select>
            </div>
        @endif
        <div class="flex gap-2 self-end sm:self-auto sm:ml-auto">
            <a href="{{ request()->url() }}"
               class="flex items-center gap-1 border border-slate-700 text-slate-400 rounded-lg px-4 py-2 text-sm hover:bg-slate-700 hover:text-slate-200 transition {{ request()->hasAny(['sort', 'score_min', 'date_from', 'date_to', 'proposer_id']) ? '' : 'invisible' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
                Effacer
            </a>
            <button type="submit"
                    class="bg-blue-600 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-500 transition">
                Appliquer
            </button>
        </div>
    </form>
</div>