@props([
    'users' => [], 
])

<form method="GET" action="{{ request()->url() }}"
      class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 flex flex-wrap gap-3 items-end">

    {{-- Status --}}
    <div class="flex flex-col gap-1 min-w-[150px]">
        <label for="status" class="text-xs font-medium text-gray-500">Statut</label>
        <select name="status" id="status"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
            <option value="">Tous</option>
            <option value="modification"   {{ request('status') === 'modification'  ? 'selected' : '' }}>Modification</option>
            <option value="approved"       {{ request('status') === 'approved'      ? 'selected' : '' }}>Approuvé</option>
            <option value="refused"        {{ request('status') === 'refused'       ? 'selected' : '' }}>Refusé</option>
            <option value="archived"       {{ request('status') === 'archived'      ? 'selected' : '' }}>Archivé</option>
        </select>
    </div>

    <div class="flex flex-col gap-1 min-w-[110px]">
        <label for="score_min" class="text-xs font-medium text-gray-500">Score min</label>
        <input type="number" name="score_min" id="score_min"
               min="0" max="100" step="1"
               value="{{ request('score_min') }}"
               placeholder="ex. 60"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200" />
    </div>

    <div class="flex flex-col gap-1 min-w-[140px]">
        <label for="date_from" class="text-xs font-medium text-gray-500">Du</label>
        <input type="date" name="date_from" id="date_from"
               value="{{ request('date_from') }}"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200" />
    </div>

    <div class="flex flex-col gap-1 min-w-[140px]">
        <label for="date_to" class="text-xs font-medium text-gray-500">Au</label>
        <input type="date" name="date_to" id="date_to"
               value="{{ request('date_to') }}"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200" />
    </div>

    @if(count($users))
    <div class="flex flex-col gap-1 min-w-[160px]">
        <label for="proposer_id" class="text-xs font-medium text-gray-500">Proposeur</label>
        <select name="proposer_id" id="proposer_id"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
            <option value="">Tous les proposeurs</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('proposer_id') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="flex gap-2 ml-auto">
        @if(request()->hasAny(['status', 'score_min', 'date_from', 'date_to', 'proposer_id']))
            <a href="{{ request()->url() }}"
               class="flex items-center gap-1 border border-gray-200 text-gray-500 rounded-lg px-4 py-2 text-sm hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
                Effacer
            </a>
        @endif

        <button type="submit"
                class="bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-800 transition">
            Appliquer
        </button>
    </div>

</form>