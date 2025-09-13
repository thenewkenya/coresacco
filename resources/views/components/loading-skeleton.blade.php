@props(['lines' => 3, 'height' => 'h-4'])

<div class="animate-pulse">
    @for($i = 0; $i < $lines; $i++)
        <div class="flex items-center space-x-4 mb-3">
            <div class="rounded-full bg-zinc-200 dark:bg-zinc-700 h-10 w-10"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-3/4"></div>
                <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-1/2"></div>
            </div>
        </div>
    @endfor
</div>
