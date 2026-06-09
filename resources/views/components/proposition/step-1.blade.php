<div x-show="step === 1" class="space-y-8">
    <div class="space-y-4">
        <x-proposition.input title="Title" name="titre" :value="old('titre')" />

        <x-proposition.input title="Porteur" name="porteur" :value="old('porteur')">
            <p class="text-xs text-gray-500 mb-1">Person guaranteeing this proposal</p>
        </x-proposition.input>

        <div>
            <label class="block text-sm font-medium text-gray-700">Membres</label>
            <p class="text-xs text-gray-500 mb-2">All people involved in the project</p>
            <x-proposition.repeatable-list items="membres" name="membres[]" placeholder="Name" add-label="+ Add member" />
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
        <x-proposition.next />
        <a href="#" class="text-base text-gray-600 hover:underline">Cancel</a>
    </div>
</div>
