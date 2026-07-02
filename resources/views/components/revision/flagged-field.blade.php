@props(['label', 'comment'])

<div class="bg-white shadow-sm rounded-lg p-5 mb-3 border border-amber-200">
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">
        {{ $label }}
    </p>

    <div class="mb-3 flex items-start gap-2 rounded-md bg-amber-50 border border-amber-200 p-3 text-sm text-amber-800">
        <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
        </svg>
        <span>{{ $comment }}</span>
    </div>

    {{ $slot }}
</div>
