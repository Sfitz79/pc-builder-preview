<?php

namespace Tests\Feature;

use App\Models\SoftwareProduct;
use App\Models\SoftwarePurchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SoftwareStoreTest extends TestCase
{
    use RefreshDatabase;

    private SoftwareProduct $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = SoftwareProduct::create([
            'metenzi_product_id' => 'P123456',
            'sku' => 'WIN11-PRO-OEM',
            'name' => 'Windows 11 Pro OEM Key',
            'category' => 'Operating Systems',
            'platform' => 'Windows',
            'retail_price' => 23.41,
            'retail_price_cents' => 2341,
            'gbp_price' => 19.90,
            'currency' => 'GBP',
            'stock' => 10,
            'active' => true,
            'warranty_days' => 30,
        ]);
    }

    public function test_index_page_lists_active_products(): void
    {
        config(['services.paypal.client_id' => 'test-client', 'services.paypal.secret' => 'test-secret']);

        $response = $this->get('/software');

        $response->assertOk()
            ->assertSee('Software, keys in hand.')
            ->assertSee('Windows 11 Pro OEM Key')
            ->assertSee('&pound;19.90', false)
            ->assertSee('Test Phase — Orders by Request')
            ->assertSee('not available to buy directly right now')
            ->assertSee('https://wa.me/447933101083', false)
            ->assertSee('Order on WhatsApp', false);
    }

    public function test_index_page_notes_the_store_is_not_configured_without_paypal(): void
    {
        $response = $this->get('/software');

        $response->assertOk()
            ->assertSee('The software store is not configured yet.')
            ->assertSee('Test Phase — Orders by Request');
    }

    public function test_index_lazy_sync_populates_the_catalogue_when_empty(): void
    {
        SoftwareProduct::query()->delete();

        config([
            'services.paypal.client_id' => 'test-client',
            'services.paypal.secret' => 'test-secret',
            'metenzi.api_key' => 'test-key',
        ]);

        Http::fake([
            'metenzi.com/api/public/products*' => Http::response([
                'data' => [[
                    'id' => 'P999999',
                    'sku' => 'WIN11-PRO-RETAIL',
                    'name' => 'Windows 11 Pro Retail Key',
                    'category' => 'Operating Systems',
                    'platform' => 'Windows',
                    'description' => 'Full retail licence.',
                    'shortDescription' => 'Retail key',
                    'retailPrice' => 23.41,
                    'retailPriceCents' => 2341,
                    'stock' => 5,
                    'status' => 'active',
                    'warrantyDays' => 30,
                    'imageUrl' => null,
                    'instructions' => null,
                ]],
            ], 200),
            'metenzi.com/api/public/balance*' => Http::response([
                'data' => ['availableCredit' => 10.0, 'currency' => 'EUR'],
            ], 200),
        ]);

        $response = $this->get('/software');

        $response->assertOk()
            ->assertSee('Windows 11 Pro Retail Key')
            ->assertSee('&pound;19.90', false)
            ->assertDontSee('Sync diagnostic');

        $this->assertSame(1, SoftwareProduct::count());
        $this->assertSame('P999999', SoftwareProduct::first()->metenzi_product_id);
    }

    public function test_purchase_creates_an_awaiting_payment_purchase(): void
    {
        $response = $this->postJson('/software/purchases', [
            'product_id' => $this->product->id,
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['purchase_id', 'owner_token', 'payment_url'])
            ->assertJsonPath('product.sku', 'WIN11-PRO-OEM')
            ->assertJsonPath('amount', 19.90)
            ->assertJsonPath('currency', 'GBP');

        $purchase = SoftwarePurchase::where('uuid', $response->json('purchase_id'))->firstOrFail();

        $this->assertSame(SoftwarePurchase::STATUS_PENDING, $purchase->status);
        $this->assertSame(19.90, (float) $purchase->amount_gbp);
    }

    public function test_purchase_rejects_out_of_stock_products(): void
    {
        $this->product->update(['stock' => 0]);

        $this->postJson('/software/purchases', ['product_id' => $this->product->id])
            ->assertStatus(422);
    }

    public function test_payment_page_requires_the_owner_token(): void
    {
        $purchase = SoftwarePurchase::create([
            'owner_token' => 'token-abc',
            'product_id' => $this->product->id,
            'sku' => $this->product->sku,
            'product_name' => $this->product->name,
            'amount_gbp' => $this->product->gbp_price,
            'currency' => 'GBP',
            'status' => SoftwarePurchase::STATUS_PENDING,
        ]);

        $this->get('/software/purchases/'.$purchase->uuid.'/payment')
            ->assertForbidden();

        $this->get('/software/purchases/'.$purchase->uuid.'/payment?owner_token=token-abc')
            ->assertOk()
            ->assertSee('Windows 11 Pro OEM Key')
            ->assertSee('Pay with PayPal')
            ->assertSee('not enabled yet');
    }

    public function test_paypal_endpoint_returns_503_when_not_configured(): void
    {
        $purchase = SoftwarePurchase::create([
            'owner_token' => 'token-abc',
            'product_id' => $this->product->id,
            'sku' => $this->product->sku,
            'product_name' => $this->product->name,
            'amount_gbp' => $this->product->gbp_price,
            'currency' => 'GBP',
            'status' => SoftwarePurchase::STATUS_PENDING,
        ]);

        $this->withHeader('X-Owner-Token', 'token-abc')
            ->postJson('/software/purchases/'.$purchase->uuid.'/paypal')
            ->assertStatus(503);
    }

    public function test_webhook_delivers_keys_for_a_matched_purchase(): void
    {
        $purchase = SoftwarePurchase::create([
            'owner_token' => 'token-abc',
            'product_id' => $this->product->id,
            'sku' => $this->product->sku,
            'product_name' => $this->product->name,
            'amount_gbp' => $this->product->gbp_price,
            'currency' => 'GBP',
            'status' => SoftwarePurchase::STATUS_PAID,
            'metenzi_order_id' => 'MO-777',
        ]);

        $this->postJson('/webhooks/metenzi', [
            'event' => 'order.fulfilled',
            'data' => [
                'orderId' => 'MO-777',
                'items' => [
                    ['keys' => [['code' => 'ABC-DEF-GHI', 'codeType' => 'code/text']]],
                ],
            ],
        ])->assertOk()->assertJson(['success' => true]);

        $purchase->refresh();

        $this->assertSame(SoftwarePurchase::STATUS_FULFILLED, $purchase->status);
        $this->assertSame([['code' => 'ABC-DEF-GHI', 'type' => 'code/text']], $purchase->keys);
        $this->assertSame('order.fulfilled', $purchase->last_webhook_event);
    }

    public function test_confirmation_page_renders_delivered_keys(): void
    {
        $purchase = SoftwarePurchase::create([
            'owner_token' => 'token-abc',
            'product_id' => $this->product->id,
            'sku' => $this->product->sku,
            'product_name' => $this->product->name,
            'amount_gbp' => $this->product->gbp_price,
            'currency' => 'GBP',
            'status' => SoftwarePurchase::STATUS_FULFILLED,
            'keys' => [['code' => 'ABC-DEF-GHI', 'type' => 'text/plain']],
            'fulfilled_at' => now(),
        ]);

        $response = $this->get('/software/purchases/'.$purchase->uuid.'/confirmed?owner_token=token-abc');

        $response->assertOk()
            ->assertSee('Your key is ready')
            ->assertSee('ABC-DEF-GHI')
            ->assertSee('Copy');
    }
}
