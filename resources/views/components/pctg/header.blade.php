@props(['active' => 'builder'])

<header
    class="
        sticky
        top-0
        z-50
        border-b
        border-slate-800
        bg-black/50
        backdrop-blur-xl
    "
>
    <div
        class="
            flex
            h-16
            items-center
            justify-between
            px-4
            lg:px-6
        "
    >
        <div class="flex items-center gap-4">
            <button
                x-on:click="$dispatch('open-mobile-menu')"
                class="text-slate-300 transition hover:text-white lg:hidden"
                aria-label="Open menu"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="{{ route('builder') }}" class="flex items-center gap-3">
                <span class="power-pulse flex h-9 w-9 items-center justify-center rounded-xl bg-pctg-primary text-white">
                    <x-pctg.icon name="zap" class="h-5 w-5" />
                </span>
                <span class="leading-tight">
                    <span class="block text-lg font-bold">PCTG Builder</span>
                    <span class="block text-xs text-slate-500">Get Your Gamers Edge&trade;</span>
                </span>
            </a>
        </div>

        <nav class="hidden items-center gap-6 text-sm lg:flex">
            @php
                $links = [
                    'builder' => ['Builder', route('builder')],
                    'ai' => ['AI Builder', route('builder.ai')],
                    'builds' => ['Saved Builds', route('builder.builds')],
                    'checkout' => ['Checkout', route('builder.checkout')],
                    'guides' => ['Guides', url('/best-gaming-pc-under-1500')],
                ];
            @endphp

            @foreach ($links as $key => $link)
                <a
                    href="{{ $link[1] }}"
                    class="{{ $active === $key ? 'text-white' : 'text-slate-400 transition hover:text-white' }}"
                >
                    {{ $link[0] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-4">
            <div class="h-9 w-9 rounded-full bg-red-500"></div>
        </div>
    </div>
</header>
