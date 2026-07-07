@props([
    'users',
    'name',
    'selected' => [''],
    'addLabel' => '+ Ajouter',
    'placeholder' => 'Sélectionner un utilisateur…',
])

<div x-data="userMultiSelect(@js($selected))">
    <div class="space-y-2">
        <template x-for="(item, idx) in selected" :key="item.id">
            <div class="flex gap-2">
                <select :name="'{{ $name }}[' + idx + ']'" x-model="item.value"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ $placeholder }}</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}"
                            :disabled="selected.some((s, i) => i !== idx && String(s.value) === '{{ $user->id }}')">
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                <button type="button" @click="remove(idx)"
                    x-show="selected.length > 1"
                    class="px-2 py-1 text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
            </div>
        </template>
    </div>
    <button type="button" @click="add()"
        class="mt-2 text-sm text-indigo-600 hover:underline">{{ $addLabel }}</button>
</div>
