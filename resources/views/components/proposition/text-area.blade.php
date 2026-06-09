@props([
    'section' => '',
    'name',
    'label',
    'description' => '',
    'rows' => 6,
])

<div>
    <h4 class="text-base font-semibold text-gray-800 mb-2">{{ $section }}</h4>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    @if ($description)
        <p class="text-xs text-gray-500 mb-1">{{ $description }}</p>
    @endif
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error($name)  @enderror">{{ old($name) }}</textarea>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>