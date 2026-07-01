@props([
    'messager_name' => '',
    'commentaire_msg' => '',
    'date_msg' => '',
])

<div class=" flex justify-between gap-2 rounded-lg bg-gray-50 p-3">
    <div>
        <p class="text-xs font-semibold text-gray-700">{{ $messager_name }}</p>
        <p class="mt-1 text-sm text-gray-600">{{ $commentaire_msg }}</p>
    </div>
    <p class="mt-1 text-sm text-gray-600">{{ $date_msg }}</p>
</div>
