
<div class="rounded-lg border border-gray-200 p-3 flex flex-col justify-center">
    <p class="text-sm font-semibold text-gray-800">Avancement</p>
    <div class="mt-3 h-1.5 w-full rounded-full bg-gray-100">
        <div class="h-1.5 w-1/3 rounded-full bg-emerald-400"></div>
    </div>
</div>
<div class="rounded-lg border border-gray-200 p-3">
    <p class="text-sm font-semibold text-gray-800">Details</p>
    <x-projects_Details.details/>
</div>
</div>
<div class="mt-3 flex flex-col gap-3">

    <div class="rounded-lg border border-gray-200 p-3 flex flex-col justify-between">
        <div class="flex items-center justify-between">
            <p class="text-sm font-semibold text-gray-800">Objectifs</p>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 p-3 flex flex-col justify-between">
        <div class="flex items-center justify-between">
            <p class="text-sm font-semibold text-gray-800">Livrables</p>
        </div>
    </div>
    <x-Phase-details.resources/>
</div>
