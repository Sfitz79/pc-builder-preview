@props([
    'buildTotal' => null,
    'href' => '/builder/checkout',
])

<footer
    class="
        sticky
        bottom-0
        z-40
        border-t
        border-slate-800
        bg-[#0b0d12]/90
        backdrop-blur-xl
    "
>
    <div
        class="
            flex
            flex-wrap
            items-center
            justify-between
            gap-4
            px-4
            py-4
            lg:px-6
        "
    >
        <div>
            <p class="text-xs text-slate-500">Estimated Total</p>
            <p class="text-2xl font-bold">{{ $buildTotal ?? '£1,799' }}</p>
        </div>

        <div class="hidden text-xs text-slate-500 md:block">
            Build ready &middot; Free shipping &middot; 3-year warranty
        </div>

        <x-pctg.button :href="$href">
            Checkout
        </x-pctg.button>
    </div>
</footer>
