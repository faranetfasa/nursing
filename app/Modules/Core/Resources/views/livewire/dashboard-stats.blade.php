<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ($stats as $stat)
        <div class="card">
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $stat['title'] }}</p>
            <p class="mt-2 text-2xl font-bold text-primary">{{ number_format($stat['value']) }}</p>
        </div>
    @endforeach
</div>
