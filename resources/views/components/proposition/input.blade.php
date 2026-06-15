@props(['value' => '', 'title' => '', 'name' => ''])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $title }}</label>
    {{ $slot }}
    <input type="text" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 ' . ($errors->has($name) ? 'border-red-500' : 'border-gray-300')]) }}>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
