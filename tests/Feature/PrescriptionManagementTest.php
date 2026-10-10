<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Livewire\Clients\ClientShow;
use App\Livewire\Prescriptions\PrescriptionList;
use App\Models\Client;
use App\Models\Prescription;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PrescriptionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        StoreSetting::factory()->create([
            'currency' => 'MAD',
            'city' => 'Marrakech',
        ]);

        $this->staff = User::factory()->create([
            'role' => UserRole::Staff,
        ]);

        $this->client = Client::factory()->create([
            'first_name' => 'Karim',
            'last_name' => 'El Idrissi',
            'phone' => '0661998877',
            'city' => 'Marrakech',
        ]);
    }

    public function test_guest_cannot_access_prescriptions_list(): void
    {
        $response = $this->get(route('prescriptions.index'));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_prescriptions_list(): void
    {
        $this->actingAs($this->staff);

        $prescription = Prescription::factory()->create([
            'client_id' => $this->client->id,
            'od_sphere' => -2.50,
            'og_sphere' => -2.00,
            'prescribed_by' => 'Dr. Alaoui',
            'prescription_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->get(route('prescriptions.index'));
        $response->assertStatus(200);
        $response->assertSee('Karim El Idrissi');
        $response->assertSee('Dr. Alaoui');
        $response->assertSee('-2.50');
    }

    public function test_can_create_prescription_with_positive_and_negative_optical_values(): void
    {
        Livewire::actingAs($this->staff)
            ->test(PrescriptionList::class)
            ->call('openCreateModal', $this->client->id)
            ->assertSet('showModal', true)
            ->set('prescription_date', '2026-10-09')
            ->set('prescribed_by', 'Dr. Benkirane')
            ->set('od_sphere', '-3.25')
            ->set('od_cylinder', '-0.75')
            ->set('od_axis', '110')
            ->set('od_addition', '2.00')
            ->set('og_sphere', '+1.50')
            ->set('og_cylinder', '-0.50')
            ->set('og_axis', '75')
            ->set('og_addition', '2.00')
            ->set('pd_right', '32.0')
            ->set('pd_left', '31.5')
            ->call('savePrescription')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('prescriptions', [
            'client_id' => $this->client->id,
            'prescribed_by' => 'Dr. Benkirane',
            'od_sphere' => -3.25,
            'od_cylinder' => -0.75,
            'od_axis' => 110,
            'od_addition' => 2.00,
            'og_sphere' => 1.50,
            'og_cylinder' => -0.50,
            'og_axis' => 75,
            'og_addition' => 2.00,
            'pd_right' => 32.0,
            'pd_left' => 31.5,
            'pd' => 63.5,
        ]);
    }

    public function test_prescription_validation_axis_and_addition(): void
    {
        // Invalid Axis > 180 and negative addition
        Livewire::actingAs($this->staff)
            ->test(PrescriptionList::class)
            ->call('openCreateModal', $this->client->id)
            ->set('prescription_date', '2026-10-09')
            ->set('od_axis', '200') // Invalid axis > 180
            ->set('od_addition', '-1.50') // Invalid negative addition
            ->call('savePrescription')
            ->assertHasErrors(['od_axis', 'od_addition']);
    }

    public function test_prescription_optional_fields_allow_partial_corrections(): void
    {
        // Optician prescription with only sphere (no cylinder, no axis, no addition)
        Livewire::actingAs($this->staff)
            ->test(PrescriptionList::class)
            ->call('openCreateModal', $this->client->id)
            ->set('prescription_date', '2026-10-09')
            ->set('od_sphere', '-1.75')
            ->set('og_sphere', '-1.50')
            ->call('savePrescription')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('prescriptions', [
            'client_id' => $this->client->id,
            'od_sphere' => -1.75,
            'og_sphere' => -1.50,
            'od_cylinder' => null,
            'od_axis' => null,
        ]);
    }

    public function test_prescription_history_preservation_when_creating_new_prescriptions(): void
    {
        // Old prescription (2024)
        $oldPrescription = Prescription::factory()->create([
            'client_id' => $this->client->id,
            'prescription_date' => '2024-05-10',
            'od_sphere' => -1.50,
            'og_sphere' => -1.25,
            'prescribed_by' => 'Dr. Ancien',
        ]);

        // New prescription (2026) created via ClientShow
        Livewire::actingAs($this->staff)
            ->test(ClientShow::class, ['client' => $this->client])
            ->call('openCreatePrescriptionModal')
            ->set('prescription_date', '2026-10-09')
            ->set('prescribed_by', 'Dr. Nouveau')
            ->set('od_sphere', '-2.25')
            ->set('og_sphere', '-2.00')
            ->call('savePrescription')
            ->assertHasNoErrors();

        // Both prescriptions must exist in the database
        $this->assertDatabaseCount('prescriptions', 2);
        $this->assertDatabaseHas('prescriptions', [
            'id' => $oldPrescription->id,
            'od_sphere' => -1.50,
            'prescribed_by' => 'Dr. Ancien',
        ]);
        $this->assertDatabaseHas('prescriptions', [
            'client_id' => $this->client->id,
            'od_sphere' => -2.25,
            'prescribed_by' => 'Dr. Nouveau',
        ]);

        // Client latestPrescription relationship returns the newest one
        $this->client->refresh();
        $this->assertEquals(-2.25, (float) $this->client->latestPrescription->od_sphere);
        $this->assertEquals(2, $this->client->prescriptions()->count());
    }

    public function test_guest_cannot_download_prescription_file(): void
    {
        $prescription = Prescription::factory()->create([
            'client_id' => $this->client->id,
            'prescription_file_path' => 'prescriptions/test.pdf',
        ]);
        Storage::disk('local')->put('prescriptions/test.pdf', 'dummy content');

        $response = $this->get(route('prescriptions.file', $prescription));
        $response->assertRedirect('/login');
    }

    public function test_prescription_file_upload_and_secure_download(): void
    {
        $fakeFile = UploadedFile::fake()->create('ordonnance_medicale.pdf', 300, 'application/pdf');

        Livewire::actingAs($this->staff)
            ->test(PrescriptionList::class)
            ->call('openCreateModal', $this->client->id)
            ->set('prescription_date', '2026-10-09')
            ->set('prescription_file', $fakeFile)
            ->set('od_sphere', '-1.00')
            ->call('savePrescription')
            ->assertHasNoErrors();

        $prescription = Prescription::where('client_id', $this->client->id)->first();
        $this->assertNotNull($prescription->prescription_file_path);
        Storage::disk('local')->assertExists($prescription->prescription_file_path);

        // Authenticated staff can download file securely
        $authResponse = $this->actingAs($this->staff)->get(route('prescriptions.file', $prescription));
        $authResponse->assertStatus(200);
        $authResponse->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_prescription_file_validation_rejects_invalid_mime_types(): void
    {
        $invalidFile = UploadedFile::fake()->create('script.exe', 500, 'application/x-msdownload');

        Livewire::actingAs($this->staff)
            ->test(PrescriptionList::class)
            ->call('openCreateModal', $this->client->id)
            ->set('prescription_date', '2026-10-09')
            ->set('prescription_file', $invalidFile)
            ->call('savePrescription')
            ->assertHasErrors(['prescription_file']);
    }

    public function test_can_edit_existing_prescription(): void
    {
        $prescription = Prescription::factory()->create([
            'client_id' => $this->client->id,
            'od_sphere' => -2.00,
            'prescription_date' => '2026-10-01',
        ]);

        Livewire::actingAs($this->staff)
            ->test(PrescriptionList::class)
            ->call('openEditModal', $prescription->id)
            ->assertSet('editingPrescriptionId', $prescription->id)
            ->set('od_sphere', '-2.75')
            ->call('savePrescription')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('prescriptions', [
            'id' => $prescription->id,
            'od_sphere' => -2.75,
        ]);
    }

    public function test_can_archive_prescription(): void
    {
        $prescription = Prescription::factory()->create([
            'client_id' => $this->client->id,
        ]);

        Livewire::actingAs($this->staff)
            ->test(PrescriptionList::class)
            ->call('confirmDelete', $prescription->id)
            ->assertSet('showDeleteModal', true)
            ->call('deletePrescription')
            ->assertSet('showDeleteModal', false);

        $this->assertSoftDeleted('prescriptions', [
            'id' => $prescription->id,
        ]);
    }
}
