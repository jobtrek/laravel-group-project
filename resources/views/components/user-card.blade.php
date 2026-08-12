@props([
    'user' => null,
    'name' => '',
    'email' => '',
    'roles' => '',
])

@php
    use App\Enums\Role;
@endphp
<div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
    <div class="flex items-center gap-3">
        <div
            class="w-10 h-10 rounded-full bg-[#93c83a] text-[#131c3f] flex items-center justify-center font-extrabold text-sm">
            {{ mb_strtoupper(mb_substr($name, 0, 1)) }}
        </div>
        <div>
            <p class="font-semibold text-gray-900">{{ $name }}</p>
            <p class="text-sm text-gray-500">{{ $email }}</p>
        </div>
    </div>
    @if ($roles->isNotEmpty())
        <div class="flex flex-wrap gap-1.5 mt-3">
            @foreach ($roles as $role)
                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-medium">
                    {{ Role::tryFrom($role->name)->label() ?? $role->name }}
                </span>
            @endforeach
        </div>
    @endif
</div>
