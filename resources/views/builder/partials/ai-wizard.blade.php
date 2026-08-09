<x-pctg.hero>

    <x-pctg.badge>
        🤖 AI Powered Builder
    </x-pctg.badge>

    <h1 class="mt-6 text-4xl font-black md:text-6xl">
        Build Your Next PC

        <span class="text-red-500">
            With AI
        </span>
    </h1>

    <p class="mt-4 max-w-2xl text-slate-400">
        Tell us your budget and intended use.
        PCTG AI recommends optimized
        compatible components instantly.
    </p>

    <div class="mt-10 grid gap-4 md:grid-cols-4">

        <button class="pctg-card-hover text-center" @click="purpose = 'gaming'">
            🎮 Gaming
        </button>

        <button class="pctg-card-hover text-center" @click="purpose = 'streaming'">
            🎥 Streaming
        </button>

        <button class="pctg-card-hover text-center" @click="purpose = 'creation'">
            🎨 Content Creation
        </button>

        <button class="pctg-card-hover text-center" @click="purpose = 'ai'">
            🤖 AI Development
        </button>

    </div>

    <div class="mt-8 grid gap-4 md:grid-cols-3">

        <input
            type="number"
            placeholder="Budget £"
            class="pctg-input"
            x-model.number="budget"
        >

        <select class="pctg-input" x-model="resolution">

            <option>1080P</option>
            <option>1440P</option>
            <option>4K</option>

        </select>

        <x-pctg.button @click="generateBuild()">
            <span x-show="!loading">Generate AI Build</span>
            <span x-show="loading">Generating…</span>
        </x-pctg.button>

    </div>

</x-pctg.hero>
