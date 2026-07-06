
@props(['fieldKey', 'label'])

<div class="bg-white shadow-sm rounded-lg p-5 mb-3 border border-gray-100">

    <div class="flex items-start justify-between gap-6">

        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-1">
                {{ $label }}
            </p>
            {{ $slot }}
        </div>

        <label class="flex items-center gap-2 shrink-0 cursor-pointer select-none group mt-1">
            <input
                type="checkbox"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                @change="fields['{{ $fieldKey }}'].checked = $event.target.checked"
                :checked="fields['{{ $fieldKey }}'].checked"
            >
            <span class="text-sm text-gray-500 group-hover:text-gray-700 transition">Annoter</span>
        </label>
    </div>

    <div x-show="fields['{{ $fieldKey }}'].checked" x-cloak class="mt-4">
        <label class="block text-sm text-gray-500 mb-1">Votre commentaire pour le proposeur :</label>

        <textarea
            name="field_comments[{{ $fieldKey }}]"
            rows="3"
            placeholder="Expliquez ce qui doit être précisé ou corrigé..."
            class="w-full rounded-md border-gray-300 shadow-sm text-sm
                   focus:ring-indigo-500 focus:border-indigo-500
                   placeholder-gray-400 resize-none"
            :disabled="!fields['{{ $fieldKey }}'].checked"
            x-model="fields['{{ $fieldKey }}'].value"
        ></textarea>
    </div>
</div>