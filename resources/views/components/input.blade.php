@props(['title', 'name', 'value' => null, 'type' => 'text'])

<div>
    <label for="{{ $name }}" class="block text-base font-medium text-gray-700">{{ $title }}</label>
    <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $value ?? old($name) }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base @error($name) cle @enderror">
    @error($name)
        <p class="mt-1 text-base text-red-600">{{ $message }}</p>
    @enderror
</div>