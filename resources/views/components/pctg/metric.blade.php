@props([
    'title',
    'value'
])

<div class="pctg-metric">
    <p class="text-xs text-slate-500">
        {{ $title }}
    </p>

    <h3 class="mt-2 text-2xl font-bold">
        {{ $value }}
    </h3>

    {{ $slot }}
</div>
