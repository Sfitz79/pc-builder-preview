<x-pctg.layouts.builder
    title="Review & Checkout"
    active="checkout"
    buildTotal="$2,849"
    :selectedCount="8"
    :buildProgress="80"
>
    <x-pctg.section-heading
        icon="wallet"
        title="Review & Checkout"
        subtitle="Confirm your selections before placing your order"
    >
        <x-slot:action>
            <x-pctg.badge variant="success" dot>Build ready</x-pctg.badge>
        </x-slot:action>
    </x-pctg.section-heading>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <x-pctg.card :padded="false" class="overflow-hidden">
                <div class="border-b border-white/5 p-6">
                    <h2 class="flex items-center gap-2 font-display text-lg font-bold text-white">
                        <x-pctg.icon name="shopping-cart" class="h-5 w-5 text-pctg-primary-hover" />
                        Selected components
                    </h2>
                </div>

                <ul class="divide-y divide-white/5">
                    @php
                        $items = [
                            ['icon' => 'cpu', 'name' => 'AMD Ryzen 7 9800X3D', 'detail' => 'AM5 Â· 8 Core Â· 5.2 GHz', 'price' => '$479'],
                            ['icon' => 'gpu', 'name' => 'NVIDIA GeForce RTX 5080', 'detail' => '16GB GDDR7 Â· Blackwell', 'price' => '$999'],
                            ['icon' => 'memory-stick', 'name' => 'Corsair Vengeance 32GB', 'detail' => 'DDR5-6000 CL30', 'price' => '$109'],
                            ['icon' => 'layers', 'name' => 'ASUS ROG Strix X870E-E', 'detail' => 'AM5 Â· WiFi 7', 'price' => '$449'],
                            ['icon' => 'hard-drive', 'name' => 'Samsung 990 Pro 2TB', 'detail' => 'PCIe 5.0 NVMe', 'price' => '$189'],
                            ['icon' => 'computer', 'name' => 'NZXT H6 Flow RGB', 'detail' => 'Mid Tower Â· ATX', 'price' => '$129'],
                            ['icon' => 'fan', 'name' => 'Lian Li Galahad II 360', 'detail' => '360mm AIO', 'price' => '$159'],
                            ['icon' => 'power', 'name' => 'Corsair RM1000x Shift', 'detail' => 'ATX 3.1 Â· 80+ Gold', 'price' => '$189'],
                        ];
                    @endphp

                    @foreach ($items as $item)
                        <li class="flex items-center gap-4 p-5">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-pctg-elevated text-pctg-text-secondary">
                                <x-pctg.icon :name="$item['icon']" class="h-5 w-5" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-white">{{ $item['name'] }}</p>
                                <p class="mt-0.5 text-xs text-pctg-text-secondary">{{ $item['detail'] }}</p>
                            </div>
                            <p class="shrink-0 font-display text-sm font-bold text-white">{{ $item['price'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </x-pctg.card>

            <x-pctg.card class="p-6">
                <h2 class="font-display text-lg font-bold text-white">Compatibility</h2>
                <p class="mt-1 text-sm text-pctg-text-secondary">All components passed PCTG AI verification.</p>
                <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="flex items-center gap-3 rounded-2xl border border-white/5 bg-pctg-elevated/60 p-4">
                        <x-pctg.icon name="shield-check" class="h-5 w-5 shrink-0 text-pctg-success" />
                        <p class="text-sm text-pctg-text-secondary">Physical fitment and clearance verified</p>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl border border-white/5 bg-pctg-elevated/60 p-4">
                        <x-pctg.icon name="gauge" class="h-5 w-5 shrink-0 text-pctg-warning" />
                        <p class="text-sm text-pctg-text-secondary">BIOS update required on arrival</p>
                    </div>
                </div>
            </x-pctg.card>
        </div>

        <x-pctg.card :padded="false" class="h-fit overflow-hidden lg:sticky lg:top-24">
            <div class="space-y-4 p-6">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.14em] text-pctg-text-secondary">Total cost</p>
                    <p class="mt-1 font-display text-4xl font-bold text-white">$2,849</p>
                    <p class="mt-1 text-sm text-pctg-text-secondary">or <span class="font-semibold text-white">$79/mo</span> for 36 months</p>
                </div>

                <dl class="space-y-2 border-t border-white/5 pt-4 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-pctg-text-secondary">Subtotal</dt>
                        <dd class="font-medium text-white">$2,702</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-pctg-text-secondary">Assembly</dt>
                        <dd class="font-medium text-white">$99</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-pctg-text-secondary">Warranty</dt>
                        <dd class="font-medium text-pctg-success">3 years included</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-pctg-text-secondary">Shipping</dt>
                        <dd class="font-medium text-white">Free</dd>
                    </div>
                </dl>

                <x-pctg.button href="/builder/checkout/payment" variant="primary" size="lg" class="w-full">
                    <x-pctg.icon name="credit-card" class="h-5 w-5" /> Continue to payment
                </x-pctg.button>

                <p class="text-center text-xs text-pctg-text-secondary">
                    Secure checkout Â· 30-day returns Â· 24/7 support
                </p>
            </div>
        </x-pctg.card>
    </div>
</x-pctg.layouts.builder>
