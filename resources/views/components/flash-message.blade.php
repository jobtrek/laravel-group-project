@props(['type' => 'success'])

@php
    $message = session('success') ?? session('status') ?? session('error');
    $type = session('error') ? 'error' : $type;
@endphp

@if ($message)
    <div x-data="{ show: true }" x-show="show" x-transition
         x-init="setTimeout(() => show = false, 4000)"
         class="mb-4 p-4 rounded-md text-sm font-medium
                {{ $type === 'error' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }}">
        <div class="flex justify-between items-center">
            <span>{{ $message }}</span>
            <button @click="show = false" class="ml-4 text-current opacity-50 hover:opacity-100">&times;</button>
        </div>
    </div>
@endif