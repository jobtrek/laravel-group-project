@props(['items', 'name', 'placeholder' => '', 'addLabel' => '+ Ajouter'])

<div class="space-y-2">
    <template x-for="(item, idx) in {{ $items }}" :key="idx">
        <div class="flex gap-2">
            <input type="text" name="{{ $name }}" x-model="{{ $items }}[idx]"
                placeholder="{{ $placeholder }}"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button type="button" @click="{{ $items }}.splice(idx, 1)"
                x-show="{{ $items }}.length > 1"
                class="px-2 py-1 text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
        </div>
    </template>
</div>
<button type="button" @click="{{ $items }}.push('')"
    class="mt-2 text-sm text-indigo-600 hover:underline">{{ $addLabel }}</button>
