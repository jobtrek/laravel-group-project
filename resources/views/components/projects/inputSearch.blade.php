@props([
    'text' => '',
    'error' => ''
])
<div>
    <input type="text"
        class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <p class="mt-1 text-sm text-red-600">{{ "$error" }}</p>
</div>
