@props(['title', 'value', 'type' => 'text'])

<div>
    <label for="title" class="block text-base font-medium text-gray-700">{{ $title }}</label>
    <input type="{{ $type }}" id="title" name="title" value="{{ $value ?? old('title') }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base @error('title') border-red-500 @enderror">
    @error('title')
        <p class="mt-1 text-base text-red-600">{{ $message }}</p>
    @enderror
</div>