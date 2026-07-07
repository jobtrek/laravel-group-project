@props([
    'progress' => 0,
])

<?php



use Carbon\Carbon;
$bgColor = '';
use App\Http\Controllers\ProjectController;

?>


<div class="relative z-10 pointer-events-none flex items-center gap-2 w-full">
    <div class="w-full bg-gray-200 rounded-full h-1.5 flex overflow-hidden">

        <div class="{{ $progress <= 20 ? 'bg-red-500' : 'bg-emerald-400' }} h-full"
             style="width: {{ min($progress, 100) }}%"></div>

        @if($progress > 100)
            <div class="bg-blue-500 h-full"
                 style="width: {{ $progress - 100 }}%"></div>
        @endif

    </div>
    <span class="text-xs font-semibold text-gray-700 whitespace-nowrap">
        {{ $progress }}%
    </span>
</div>
