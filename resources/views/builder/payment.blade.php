<x-pctg.layouts.builder
    title="Payment"
    active="checkout"
    buildTotal="$2,849"
    :selectedCount="8"
    :buildProgress="80"
>
    <x-pctg.section-heading
        icon="credit-card"
        title="Payment"
        subtitle="Secure checkout â€” no payment provider is wired yet, this is the UI shell."
    />

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <x-pctg.card :padded="false" class="overflow-hidden">
                <div class="border-b border-white/5 p-6">
                    <h2 class="flex items-center gap-2 font-display text-lg font-bold text-white">
                        <x-pctg.icon name="wallet" class="h-5 w-5 text-pctg-primary-hover" />
                        Payment details
                    </h2>
                </div>

                <div class="space-y-4 p-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="card-name" class="mb-2 block text-sm font-medium text-pctg-text-secondary">Name on card</label>
                            <input id="card-name" type="text" placeholder="Jordan Smith" class="pctg-input">
                        </div>

                        <div>
                            <label for="card-number" class="mb-2 block text-sm font-medium text-pctg-text-secondary">Card number</label>
                            <input id="card-number" type="text" inputmode="numeric" placeholder="4242 4242 4242 4242" class="pctg-input">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label for="card-expiry" class="mb-2 block text-sm font-medium text-pctg-text-secondary">Expiry</label>
                            <input id="card-expiry" type="text" placeholder="MM/YY" class="pctg-input">
                        </div>

                        <div>
                            <label for="card-cvc" class="mb-2 block text-sm font-medium text-pctg-text-secondary">CVC</label>
                            <input id="card-cvc" type="text" inputmode="numeric" placeholder="123" class="pctg-input">
                        </div>

                        <div>
                            <label for="card-postcode" class="mb-2 block text-sm font-medium text-pctg-text-secondary">Postcode</label>
                            <input id="card-postcode" type="text" placeholder="SW1A 1AA" class="pctg-input">
                        </div>
                    </div>
                </div>
            </x-pctg.card>

            <x-pctg.card>
                <h2 class="flex items-center gap-2 font-display text-lg font-bold text-white">
                    <x-pctg.icon name="shield-check" class="h-5 w-5 text-pctg-success" />
                    Order protection
                </h2>
                <p class="mt-2 text-sm text-pctg-text-secondary">
                    Pay securely with industry-standard encryption. Card details are handled by
                    the payment provider â€” never stored on our servers.
                </p>
            </x-pctg.card>
        </div>

        <x-pctg.card :padded="false" class="h-fit overflow-hidden lg:sticky lg:top-24">
            <div class="space-y-4 p-6">
                <p class="text-xs font-medium uppercase tracking-[0.14em] text-pctg-text-secondary">Order summary</p>
                <p class="font-display text-4xl font-bold text-white">$2,849</p>

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
                        <dt class="text-pctg-text-secondary">Shipping</dt>
                        <dd class="font-medium text-white">Free</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-pctg-text-secondary">VAT</dt>
                        <dd class="font-medium text-pctg-success">Included</dd>
                    </div>
                </dl>

                <x-pctg.button href="/builder" variant="primary" size="lg" class="w-full">
                    <x-pctg.icon name="credit-card" class="h-5 w-5" /> Pay $2,849
                </x-pctg.button>

                <p class="text-center text-xs text-pctg-text-secondary">
                    Demo checkout &middot; payment processing lands in a later phase
                </p>
            </div>
        </x-pctg.card>
    </div>
</x-pctg.layouts.builder>
