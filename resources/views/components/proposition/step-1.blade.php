@props(['users'])

<div x-show="step === 1" class="space-y-8">
    <div class="space-y-4">
        <x-proposition.input title="Title" name="titre" :value="old('titre')" />

        <div>
            <label class="block text-sm font-medium text-gray-700">Porteur</label>
            <p class="text-xs text-gray-500 mb-1">Person guaranteeing this proposal</p>
            <select name="porteur" x-model="porteur"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Select a user…</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('porteur') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Membres</label>
            <p class="text-xs text-gray-500 mb-2">All people involved in the project</p>
            <div class="space-y-2">
                <template x-for="(membre, idx) in membres" :key="idx">
                    <div class="flex gap-2">
                        <select :name="'membres[' + idx + ']'" x-model="membres[idx]"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select a user…</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" @click="membres.splice(idx, 1)"
                            x-show="membres.length > 1"
                            class="px-2 py-1 text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
                    </div>
                </template>
            </div>
            <button type="button" @click="membres.push('')"
                class="mt-2 text-sm text-indigo-600 hover:underline">+ Add member</button>
        </div>
    </div>

    <hr class="border-gray-200">

    <x-proposition.text-area name="description" section="Section 1 — Description"
        label="Description"
        description="Short description of the project. Max 3 paragraphs." />

    <hr class="border-gray-200">

    <div>
        <h4 class="text-base font-semibold text-gray-800 mb-1">Section 2 — Buts</h4>
        <p class="text-xs text-gray-500 mb-3">Main goals of the project. Should be SMART and aligned with the foundation's mission/vision.</p>
        <x-proposition.repeatable-list items="buts" name="buts[]" placeholder="Goal" add-label="+ Add goal" />
    </div>

    <hr class="border-gray-200">

    <x-proposition.text-area name="perimetre" section="Section 3 — Périmètre"
        label="Périmètre" description="What will the project will and will not do." />

    <div class="flex items-center gap-4 pt-2">
        <a href="#" class="text-base text-gray-600 hover:underline">Cancel</a>
        <x-proposition.next />
    </div>
</div>
