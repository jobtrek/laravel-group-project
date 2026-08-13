@props([
    'user' => null,
    'name' => '',
    'email' => '',
    'slotPosition' => 'inline',
])

<div {{ $attributes->class(['bg-white rounded-xl border border-gray-200 p-5 shadow-sm']) }}>
    <div class="flex items-center gap-3">
        <div
            class="w-10 h-10 rounded-full bg-[#93c83a] text-[#131c3f] flex items-center justify-center font-extrabold text-sm">
            {{ mb_strtoupper(mb_substr($name, 0, 1)) }}
        </div>
        <div>
            <p class="font-semibold text-gray-900">{{ $name }}</p>
            <p class="text-sm text-gray-500">{{ $email }}</p>
        </div>
        @if ($slotPosition !== 'below')
            {{ $slot }}
        @endif
    </div>
    @if ($slotPosition === 'below')
        {{ $slot }}
    @endif
</div>
