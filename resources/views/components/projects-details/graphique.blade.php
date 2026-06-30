@props([
    'porte' => 60,
    'confiance' => 30,
    'effort' => 90,
])

<div>
    <h2 class="mb-2 text-sm font-semibold text-gray-800">Portée / Confiance / Effort</h2>
    <div class="flex items-end gap-4">
        <div class="flex flex-1 flex-col items-center">
            <div class="flex h-[20rem] w-[5rem] items-end bg-gray-200">
                <div class="w-full bg-indigo-600 rounded-md hover:bg-indigo-700" style="height: {{ $porte }}%"></div>
            </div>
            <span class="mt-1 text-xs text-gray-500">Portée</span>
        </div>
        <div class="flex flex-1 flex-col items-center">
            <div class="flex h-[20rem] w-[5rem] items-end bg-gray-200">
                <div class="w-full bg-indigo-600 rounded-md hover:bg-indigo-700" style="height: {{ $confiance }}%"></div>
            </div>
            <span class="mt-1 text-xs text-gray-500">Confiance</span>
        </div>
        <div class="flex flex-1 flex-col items-center">
            <div class="flex h-[20rem] w-[5rem] items-end bg-gray-200">
                <div class="w-full bg-indigo-600 rounded-md hover:bg-indigo-700" style="height: {{ $effort }}%"></div>
            </div>
            <span class="mt-1 text-xs text-gray-500">Effort</span>
        </div>
    </div>
</div>
