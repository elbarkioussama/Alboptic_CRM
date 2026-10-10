<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Livewire\Clients\ClientList;
use App\Livewire\Clients\ClientShow;
use App\Models\Client;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        StoreSetting::factory()->create([
            'currency' => 'MAD',
            'city' => 'Marrakech',
        ]);

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->staff = User::factory()->create([
            'role' => UserRole::Staff,
        ]);
    }

    public function test_guest_cannot_access_client_list(): void
    {
        $response = $this->get('/clients');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_client_list(): void
    {
        $this->actingAs($this->staff);

        $client = Client::factory()->create([
            'first_name' => 'Fatima',
            'last_name' => 'Zahra',
            'phone' => '0661122334',
            'city' => 'Marrakech',
        ]);

        $response = $this->get('/clients');
        $response->assertStatus(200);
        $response->assertSee('Fatima Zahra');
        $response->assertSee('0661122334');
    }

    public function test_client_search_by_name_and_phone(): void
    {
        $client1 = Client::factory()->create([
            'first_name' => 'Youssef',
            'last_name' => 'El Mansouri',
            'phone' => '0661001122',
        ]);

        $client2 = Client::factory()->create([
            'first_name' => 'Samira',
            'last_name' => 'Benali',
            'phone' => '0662334455',
        ]);

        // Search by first name
        Livewire::actingAs($this->staff)
            ->test(ClientList::class)
            ->set('search', 'Youssef')
            ->assertSee('Youssef El Mansouri')
            ->assertDontSee('Samira Benali');

        // Search by phone with spaces / formatting
        Livewire::actingAs($this->staff)
            ->test(ClientList::class)
            ->set('search', '06 62 33 44 55')
            ->assertSee('Samira Benali')
            ->assertDontSee('Youssef El Mansouri');
    }

    public function test_can_create_client_with_validation(): void
    {
        Livewire::actingAs($this->staff)
            ->test(ClientList::class)
            ->call('openCreateModal')
            ->assertSet('showModal', true)
            ->set('first_name', 'Amine')
            ->set('last_name', 'Tazi')
            ->set('phone', '0665998877')
            ->set('email', 'amine.tazi@example.com')
            ->set('city', 'Marrakech')
            ->set('gender', 'male')
            ->call('saveClient')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('clients', [
            'first_name' => 'Amine',
            'last_name' => 'Tazi',
            'phone' => '0665998877',
            'email' => 'amine.tazi@example.com',
            'city' => 'Marrakech',
        ]);
    }

    public function test_client_creation_validates_required_fields(): void
    {
        Livewire::actingAs($this->staff)
            ->test(ClientList::class)
            ->call('openCreateModal')
            ->set('first_name', '')
            ->set('last_name', '')
            ->set('phone', '')
            ->call('saveClient')
            ->assertHasErrors(['first_name', 'last_name', 'phone']);
    }

    public function test_can_edit_existing_client(): void
    {
        $client = Client::factory()->create([
            'first_name' => 'Khadija',
            'last_name' => 'Alami',
            'phone' => '0661223344',
        ]);

        Livewire::actingAs($this->staff)
            ->test(ClientList::class)
            ->call('openEditModal', $client->id)
            ->assertSet('editingClientId', $client->id)
            ->assertSet('first_name', 'Khadija')
            ->set('phone', '0669998877')
            ->call('saveClient')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'phone' => '0669998877',
        ]);
    }

    public function test_can_safely_delete_client_without_orders(): void
    {
        $client = Client::factory()->create([
            'first_name' => 'Client',
            'last_name' => 'SansCommande',
            'phone' => '0660000000',
        ]);

        Livewire::actingAs($this->staff)
            ->test(ClientList::class)
            ->call('confirmDelete', $client->id)
            ->assertSet('showDeleteModal', true)
            ->assertSet('deleteWarning', null)
            ->call('deleteClient')
            ->assertSet('showDeleteModal', false);

        $this->assertSoftDeleted('clients', [
            'id' => $client->id,
        ]);
    }

    public function test_client_profile_renders_complete_information(): void
    {
        $this->actingAs($this->staff);

        $client = Client::factory()->create([
            'first_name' => 'Hassan',
            'last_name' => 'Berrada',
            'phone' => '0661445566',
            'city' => 'Marrakech',
            'notes' => 'Patient préfère les montures titane ultra-légères.',
        ]);

        $prescription = Prescription::factory()->create([
            'client_id' => $client->id,
            'od_sphere' => -2.25,
            'od_cylinder' => -0.75,
            'od_axis' => 90,
            'od_addition' => 1.50,
            'og_sphere' => -1.75,
            'og_cylinder' => -0.50,
            'og_axis' => 85,
            'og_addition' => 1.50,
            'pd_right' => 31.5,
            'pd_left' => 31.0,
            'prescribed_by' => 'Dr. Bennani',
        ]);

        $order = Order::factory()->create([
            'client_id' => $client->id,
            'prescription_id' => $prescription->id,
            'order_number' => 'CMD-2026-TEST1',
            'total_amount' => 2500.00,
            'amount_paid' => 1500.00,
            'remaining_amount' => 1000.00,
            'status' => OrderStatus::InProgress,
            'payment_status' => PaymentStatus::PartiallyPaid,
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 1500.00,
            'payment_method' => PaymentMethod::Cash,
        ]);

        $response = $this->get(route('clients.show', $client));
        $response->assertStatus(200);

        // Check header & info
        $response->assertSee('Hassan Berrada');
        $response->assertSee('0661445566');

        // Check clinical prescription values
        $response->assertSee('-2.25');
        $response->assertSee('-1.75');
        $response->assertSee('Dr. Bennani');

        // Check order and financial summaries
        $response->assertSee('CMD-2026-TEST1');
        $response->assertSee('2 500,00');
        $response->assertSee('1 500,00');
        $response->assertSee('1 000,00');

        // Check observations/notes
        $response->assertSee('Patient préfère les montures titane');
    }

    public function test_can_update_client_clinical_notes_inline(): void
    {
        $client = Client::factory()->create([
            'notes' => 'Ancienne note',
        ]);

        Livewire::actingAs($this->staff)
            ->test(ClientShow::class, ['client' => $client])
            ->set('notes', 'Nouvelle observation optique : sensibilité à la lumière bleue.')
            ->call('saveNotes');

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'notes' => 'Nouvelle observation optique : sensibilité à la lumière bleue.',
        ]);
    }
}
