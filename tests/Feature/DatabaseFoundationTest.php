<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Enums\ItemType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ProductCategory;
use App\Enums\ReminderStatus;
use App\Enums\ReminderType;
use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Document;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\Reminder;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_be_created_with_roles_and_soft_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $this->assertEquals(UserRole::Admin, $admin->role);
        $this->assertTrue($admin->isAdmin());

        $optician = User::factory()->optician()->create();
        $this->assertEquals(UserRole::Optician, $optician->role);
        $this->assertTrue($optician->isOptician());

        $optician->delete();
        $this->assertSoftDeleted($optician);
        $this->assertEquals(1, User::count());
        $this->assertEquals(2, User::withTrashed()->count());
    }

    public function test_store_settings_has_singleton_accessor_with_moroccan_defaults(): void
    {
        $setting = StoreSetting::current();

        $this->assertEquals('ALBOPTIC', $setting->business_name);
        $this->assertEquals('MAD', $setting->currency);
        $this->assertEquals('20.00', $setting->default_tax_rate);
        $this->assertEquals('Marrakech', $setting->city);
    }

    public function test_client_automatically_builds_full_name_and_handles_relationships(): void
    {
        $client = Client::create([
            'first_name' => 'Tariq',
            'last_name' => 'Ibn Ziyad',
            'phone' => '+212 6 12 34 56 78',
            'email' => 'tariq@example.ma',
        ]);

        $this->assertEquals('Tariq Ibn Ziyad', $client->full_name);

        // Add 2 prescriptions
        $oldP = Prescription::factory()->create([
            'client_id' => $client->id,
            'prescription_date' => '2025-01-10',
        ]);
        $newP = Prescription::factory()->create([
            'client_id' => $client->id,
            'prescription_date' => '2026-03-15',
        ]);

        $this->assertCount(2, $client->prescriptions);
        $this->assertEquals($newP->id, $client->latestPrescription->id);

        // Test search scope
        $this->assertEquals(1, Client::search('Tariq')->count());
        $this->assertEquals(1, Client::search('12 34 56')->count());
        $this->assertEquals(0, Client::search('NonExistentName')->count());
    }

    public function test_prescriptions_support_positive_and_negative_optical_values(): void
    {
        $client = Client::factory()->create();

        $prescription = Prescription::create([
            'client_id' => $client->id,
            'prescription_date' => '2026-10-08',
            'od_sphere' => -3.75,
            'od_cylinder' => -1.25,
            'od_axis' => 75,
            'od_addition' => 1.50,
            'og_sphere' => 2.50,
            'og_cylinder' => -0.50,
            'og_axis' => 105,
            'og_addition' => 1.50,
            'pd' => 63.5,
            'pd_right' => 31.5,
            'pd_left' => 32.0,
        ]);

        $this->assertEquals('-3.75', $prescription->od_sphere);
        $this->assertEquals('2.50', $prescription->og_sphere);
        $this->assertEquals(75, $prescription->od_axis);
        $this->assertStringContainsString('SPH: -3.75', $prescription->od_summary);
        $this->assertStringContainsString('SPH: +2.50', $prescription->og_summary);
    }

    public function test_products_support_categories_and_stock(): void
    {
        $frame = Product::factory()->create([
            'category' => ProductCategory::Frame,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $lens = Product::factory()->create([
            'category' => ProductCategory::Lens,
            'stock_quantity' => 0,
            'is_active' => true,
        ]);

        $this->assertTrue($frame->isInStock());
        $this->assertFalse($lens->isInStock());
        $this->assertEquals(1, Product::byCategory(ProductCategory::Frame)->count());
        $this->assertEquals(1, Product::byCategory(ProductCategory::Lens)->count());
    }

    public function test_complete_order_hierarchy_and_cascade_behavior(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();
        $prescription = Prescription::factory()->create(['client_id' => $client->id]);
        $product = Product::factory()->create(['selling_price' => 1500.00]);

        $order = Order::create([
            'client_id' => $client->id,
            'prescription_id' => $prescription->id,
            'order_number' => 'BC-2026-999999',
            'order_date' => now()->toDateString(),
            'status' => OrderStatus::Confirmed,
            'subtotal' => 1500.00,
            'discount' => 0.00,
            'tax_rate' => 20.00,
            'tax_amount' => 300.00,
            'total_amount' => 1800.00,
            'amount_paid' => 500.00,
            'remaining_amount' => 1300.00,
            'payment_status' => PaymentStatus::PartiallyPaid,
            'created_by' => $user->id,
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'item_type' => ItemType::Frame,
            'description' => 'Monture Test',
            'quantity' => 1,
            'unit_price' => 1500.00,
            'discount' => 0.00,
            'tax_rate' => 20.00,
            'subtotal' => 1500.00,
            'total' => 1800.00,
            'metadata' => ['color' => 'Black Gold'],
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 500.00,
            'payment_date' => now()->toDateString(),
            'payment_method' => PaymentMethod::Cash,
            'created_by' => $user->id,
        ]);

        $document = Document::create([
            'order_id' => $order->id,
            'document_type' => DocumentType::BonCommande,
            'document_number' => 'BC-2026-999999',
            'generated_at' => now(),
        ]);

        $reminder = Reminder::create([
            'client_id' => $client->id,
            'order_id' => $order->id,
            'reminder_type' => ReminderType::Pickup,
            'reminder_date' => now()->toDateString(),
            'status' => ReminderStatus::Pending,
        ]);

        $this->assertEquals($client->id, $order->client->id);
        $this->assertEquals($prescription->id, $order->prescription->id);
        $this->assertCount(1, $order->items);
        $this->assertCount(1, $order->payments);
        $this->assertCount(1, $order->documents);
        $this->assertCount(1, $order->reminders);
        $this->assertEquals('Black Gold', $order->items->first()->metadata['color']);

        // Check client outstanding balance calculation
        $this->assertEquals(1300.00, $client->totalOutstandingDebt());

        // Check reminder scope
        $this->assertEquals(1, Reminder::pending()->count());
        $this->assertEquals(1, Reminder::dueToday()->count());

        // Test Soft Delete on Order
        $order->delete();
        $this->assertSoftDeleted($order);
    }
}
