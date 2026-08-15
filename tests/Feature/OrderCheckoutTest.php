<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Component;
use App\Models\Manufacturer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private array $categories = [];

    private array $components = [];

    protected function setUp(): void
    {
        parent::setUp();

        $manufacturer = Manufacturer::create([
            'name' => 'Test Manufacturer',
            'slug' => 'test-manufacturer',
        ]);

        foreach (['cpu', 'gpu', 'case'] as $slug) {
            $this->categories[$slug] = Category::create([
                'name' => ucfirst($slug),
                'slug' => $slug,
            ])->id;
        }

        $this->components = [
            'cpu' => Component::create([
                'category_id' => $this->categories['cpu'],
                'manufacturer_id' => $manufacturer->id,
                'name' => 'AMD Ryzen 7 9800X3D',
                'slug' => 'ryzen-7-9800x3d',
                'price' => 479.00,
                'currency' => 'GBP',
                'stock' => 5,
                'specs' => ['cores' => 8, 'threads' => 16],
            ]),
            'gpu' => Component::create([
                'category_id' => $this->categories['gpu'],
                'manufacturer_id' => $manufacturer->id,
                'name' => 'RTX 5080',
                'slug' => 'rtx-5080',
                'price' => 999.00,
                'currency' => 'GBP',
                'stock' => 5,
            ]),
            'case' => Component::create([
                'category_id' => $this->categories['case'],
                'manufacturer_id' => $manufacturer->id,
                'name' => 'NZXT H6 Flow',
                'slug' => 'nzxt-h6-flow',
                'price' => 129.00,
                'currency' => 'GBP',
                'stock' => 5,
            ]),
        ];
    }

    private function selection(): array
    {
        return [
            'components' => collect($this->components)
                ->map(fn (Component $c, string $category) => ['category' => $category, 'id' => $c->id])
                ->values()
                ->all(),
        ];
    }

    public function test_order_is_created_from_selection_with_db_prices(): void
    {
        $response = $this->postJson('/builder/orders', $this->selection());

        $response->assertCreated()
            ->assertJsonPath('amounts.parts_total', 1607)
            ->assertJsonPath('amounts.currency', 'GBP')
            ->assertJsonCount(3, 'line_items')
            ->assertJsonStructure(['order_id', 'owner_token', 'mockup_url', 'share_slug']);

        $amounts = $response->json('amounts');
        $this->assertEquals((float) config('pricing.build_delivery'), (float) $amounts['build_delivery']);

        $this->assertDatabaseHas('orders', [
            'uuid' => $response->json('order_id'),
            'status' => Order::STATUS_DRAFT,
        ]);
    }

    public function test_order_store_rejects_unknown_components(): void
    {
        $response = $this->postJson('/builder/orders', [
            'components' => [
                ['category' => 'cpu', 'id' => 999999],
            ],
        ]);

        $response->assertUnprocessable();
    }

    public function test_order_requires_valid_categories(): void
    {
        $response = $this->postJson('/builder/orders', [
            'components' => [
                ['category' => 'hoverboard', 'id' => $this->components['cpu']->id],
            ],
        ]);

        $response->assertUnprocessable();
    }

    public function test_order_show_requires_matching_owner_token(): void
    {
        $created = $this->postJson('/builder/orders', $this->selection())->json();

        $this->getJson('/builder/orders/' . $created['order_id'])
            ->assertForbidden();

        $expectedSubtotal = 1607.0 + (float) config('pricing.build_delivery');
        $expectedTotal = round($expectedSubtotal * (1 + (float) config('pricing.paypal_fee_rate')), 2);

        $this->withHeader('X-Owner-Token', $created['owner_token'])
            ->getJson('/builder/orders/' . $created['order_id'])
            ->assertOk()
            ->assertJsonPath('line_items.0.name', 'AMD Ryzen 7 9800X3D');

        $amounts = $this->withHeader('X-Owner-Token', $created['owner_token'])
            ->getJson('/builder/orders/' . $created['order_id'])
            ->json('amounts');

        $this->assertEquals($expectedSubtotal, (float) $amounts['subtotal']);
        $this->assertEquals($expectedTotal, (float) $amounts['total']);
    }

    public function test_confirmed_page_renders_printable_invoice_with_terms_and_build_time(): void
    {
        $created = $this->postJson('/builder/orders', $this->selection())->json();

        $order = Order::where('uuid', $created['order_id'])->firstOrFail();
        $order->update([
            'status' => Order::STATUS_PAID,
            'paypal_capture_id' => 'CAP-123456',
            'paid_at' => now(),
        ]);

        $response = $this->get('/builder/orders/' . $order->uuid . '/confirmed');

        $response->assertOk()
            ->assertSee('Invoice')
            ->assertSee('Print / Save as PDF')
            ->assertSee('5–7 working days')
            ->assertSee(route('terms'))
            ->assertSee('AMD Ryzen 7 9800X3D')
            ->assertSee('Total paid')
            ->assertSee('@media print');
    }

    public function test_paypal_endpoint_returns_503_when_not_configured(): void
    {
        $created = $this->postJson('/builder/orders', $this->selection())->json();

        $this->withHeader('X-Owner-Token', $created['owner_token'])
            ->postJson('/builder/orders/' . $created['order_id'] . '/paypal')
            ->assertStatus(503);
    }

    public function test_mockup_endpoint_renders_a_png(): void
    {
        $created = $this->postJson('/builder/orders', $this->selection())->json();

        $response = $this->get('/builder/builds/' . $created['build_id'] . '/mockup.png');

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/png');

        $bytes = $response->getContent();
        $this->assertGreaterThan(4, strlen((string) $bytes));
        $this->assertSame(0x89, ord($bytes[0]));
    }
}
