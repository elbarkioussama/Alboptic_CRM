<?php

declare(strict_types=1);

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Models\Prescription;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Fiche Patient / Client — ALBOPTIC CRM')]
class ClientShow extends Component
{
    use WithFileUploads;

    public Client $client;

    public string $activeTab = 'overview'; // 'overview', 'prescriptions', 'orders', 'payments', 'documents', 'reminders'

    public bool $isEditingNotes = false;
    public ?string $notes = null;

    // Client edit modal state
    public bool $showEditModal = false;
    public string $first_name = '';
    public string $last_name = '';
    public string $phone = '';
    public ?string $email = null;
    public ?string $gender = null;
    public ?string $date_of_birth = null;
    public ?string $address = null;
    public string $city = 'Marrakech';

    // Prescription modal state
    public bool $showPrescriptionModal = false;
    public ?int $editingPrescriptionId = null;

    public string $prescription_date = '';
    public ?string $prescribed_by = null;
    public ?string $prescription_notes = null;

    // OD (Right Eye)
    public ?string $od_sphere = null;
    public ?string $od_cylinder = null;
    public ?string $od_axis = null;
    public ?string $od_addition = null;

    // OG (Left Eye)
    public ?string $og_sphere = null;
    public ?string $og_cylinder = null;
    public ?string $og_axis = null;
    public ?string $og_addition = null;

    // Pupillary Distances
    public ?string $pd = null;
    public ?string $pd_right = null;
    public ?string $pd_left = null;

    // Prescription file
    public $prescription_file = null;
    public ?string $existing_prescription_file_path = null;

    // Delete prescription modal
    public bool $showDeletePrescriptionModal = false;
    public ?int $deletingPrescriptionId = null;

    public function mount(Client $client): void
    {
        $this->client = $client->loadMissing([
            'prescriptions',
            'latestPrescription',
            'orders.items',
            'orders.payments',
            'orders.documents',
            'reminders',
        ]);

        $this->notes = $this->client->notes;
        $this->prescription_date = now()->format('Y-m-d');
    }

    public function openEditModal(): void
    {
        $this->first_name = $this->client->first_name;
        $this->last_name = $this->client->last_name;
        $this->phone = $this->client->phone;
        $this->email = $this->client->email;
        $this->gender = $this->client->gender;
        $this->date_of_birth = $this->client->date_of_birth?->format('Y-m-d');
        $this->address = $this->client->address;
        $this->city = $this->client->city ?? 'Marrakech';

        $this->showEditModal = true;
    }

    public function updateClient(): void
    {
        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['nullable', 'string', 'in:male,female,child'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
        ]);

        $this->client->update($validated);
        $this->client->refresh();

        $this->showEditModal = false;
        session()->flash('success', 'Coordonnées du client mises à jour avec succès.');
    }

    public function saveNotes(): void
    {
        $this->client->update(['notes' => $this->notes]);
        $this->isEditingNotes = false;
        session()->flash('success', 'Notes et observations enregistrées.');
    }

    // Prescription Actions
    public function updatedPdRight(): void
    {
        $this->autoCalculateTotalPd();
    }

    public function updatedPdLeft(): void
    {
        $this->autoCalculateTotalPd();
    }

    protected function autoCalculateTotalPd(): void
    {
        if (is_numeric($this->pd_right) && is_numeric($this->pd_left)) {
            $this->pd = (string) round((float)$this->pd_right + (float)$this->pd_left, 1);
        }
    }

    public function openCreatePrescriptionModal(): void
    {
        $this->resetPrescriptionForm();
        $this->editingPrescriptionId = null;
        $this->showPrescriptionModal = true;
    }

    public function openEditPrescriptionModal(int $id): void
    {
        $prescription = Prescription::findOrFail($id);

        $this->editingPrescriptionId = $prescription->id;
        $this->prescription_date = $prescription->prescription_date->format('Y-m-d');
        $this->prescribed_by = $prescription->prescribed_by;
        $this->prescription_notes = $prescription->notes;

        $this->od_sphere = $prescription->od_sphere !== null ? (string)$prescription->od_sphere : null;
        $this->od_cylinder = $prescription->od_cylinder !== null ? (string)$prescription->od_cylinder : null;
        $this->od_axis = $prescription->od_axis !== null ? (string)$prescription->od_axis : null;
        $this->od_addition = $prescription->od_addition !== null ? (string)$prescription->od_addition : null;

        $this->og_sphere = $prescription->og_sphere !== null ? (string)$prescription->og_sphere : null;
        $this->og_cylinder = $prescription->og_cylinder !== null ? (string)$prescription->og_cylinder : null;
        $this->og_axis = $prescription->og_axis !== null ? (string)$prescription->og_axis : null;
        $this->og_addition = $prescription->og_addition !== null ? (string)$prescription->og_addition : null;

        $this->pd = $prescription->pd !== null ? (string)$prescription->pd : null;
        $this->pd_right = $prescription->pd_right !== null ? (string)$prescription->pd_right : null;
        $this->pd_left = $prescription->pd_left !== null ? (string)$prescription->pd_left : null;

        $this->existing_prescription_file_path = $prescription->prescription_file_path;
        $this->prescription_file = null;

        $this->showPrescriptionModal = true;
    }

    public function savePrescription(): void
    {
        $validated = $this->validate([
            'prescription_date' => ['required', 'date'],
            'prescribed_by' => ['nullable', 'string', 'max:150'],
            'prescription_notes' => ['nullable', 'string', 'max:2000'],

            'od_sphere' => ['nullable', 'numeric', 'between:-30,30'],
            'od_cylinder' => ['nullable', 'numeric', 'between:-20,20'],
            'od_axis' => ['nullable', 'integer', 'between:0,180'],
            'od_addition' => ['nullable', 'numeric', 'min:0', 'max:10'],

            'og_sphere' => ['nullable', 'numeric', 'between:-30,30'],
            'og_cylinder' => ['nullable', 'numeric', 'between:-20,20'],
            'og_axis' => ['nullable', 'integer', 'between:0,180'],
            'og_addition' => ['nullable', 'numeric', 'min:0', 'max:10'],

            'pd' => ['nullable', 'numeric', 'between:20,90'],
            'pd_right' => ['nullable', 'numeric', 'between:10,50'],
            'pd_left' => ['nullable', 'numeric', 'between:10,50'],

            'prescription_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $data = [
            'client_id' => $this->client->id,
            'prescription_date' => $validated['prescription_date'],
            'prescribed_by' => $validated['prescribed_by'] ? trim($validated['prescribed_by']) : null,
            'notes' => $validated['prescription_notes'] ? trim($validated['prescription_notes']) : null,

            'od_sphere' => $validated['od_sphere'] !== null && $validated['od_sphere'] !== '' ? (float)$validated['od_sphere'] : null,
            'od_cylinder' => $validated['od_cylinder'] !== null && $validated['od_cylinder'] !== '' ? (float)$validated['od_cylinder'] : null,
            'od_axis' => $validated['od_axis'] !== null && $validated['od_axis'] !== '' ? (int)$validated['od_axis'] : null,
            'od_addition' => $validated['od_addition'] !== null && $validated['od_addition'] !== '' ? (float)$validated['od_addition'] : null,

            'og_sphere' => $validated['og_sphere'] !== null && $validated['og_sphere'] !== '' ? (float)$validated['og_sphere'] : null,
            'og_cylinder' => $validated['og_cylinder'] !== null && $validated['og_cylinder'] !== '' ? (float)$validated['og_cylinder'] : null,
            'og_axis' => $validated['og_axis'] !== null && $validated['og_axis'] !== '' ? (int)$validated['og_axis'] : null,
            'og_addition' => $validated['og_addition'] !== null && $validated['og_addition'] !== '' ? (float)$validated['og_addition'] : null,

            'pd' => $validated['pd'] !== null && $validated['pd'] !== '' ? (float)$validated['pd'] : null,
            'pd_right' => $validated['pd_right'] !== null && $validated['pd_right'] !== '' ? (float)$validated['pd_right'] : null,
            'pd_left' => $validated['pd_left'] !== null && $validated['pd_left'] !== '' ? (float)$validated['pd_left'] : null,
        ];

        if ($this->prescription_file) {
            $path = $this->prescription_file->store('prescriptions', 'local');
            $data['prescription_file_path'] = $path;
        }

        if ($this->editingPrescriptionId) {
            $prescription = Prescription::findOrFail($this->editingPrescriptionId);
            $prescription->update($data);
            session()->flash('success', "Ordonnance du {$prescription->prescription_date->format('d/m/Y')} mise à jour.");
        } else {
            $prescription = Prescription::create($data);
            session()->flash('success', "Nouvelle ordonnance ajoutée au dossier patient.");
        }

        $this->showPrescriptionModal = false;
        $this->resetPrescriptionForm();
        $this->client->load('prescriptions', 'latestPrescription');
    }

    public function deleteAttachedPrescriptionFile(): void
    {
        if ($this->editingPrescriptionId) {
            $prescription = Prescription::findOrFail($this->editingPrescriptionId);
            if ($prescription->prescription_file_path && Storage::disk('local')->exists($prescription->prescription_file_path)) {
                Storage::disk('local')->delete($prescription->prescription_file_path);
            }
            $prescription->update(['prescription_file_path' => null]);
            $this->existing_prescription_file_path = null;
            session()->flash('success', 'Document supprimé de l\'ordonnance.');
        }
    }

    public function confirmDeletePrescription(int $id): void
    {
        $this->deletingPrescriptionId = $id;
        $this->showDeletePrescriptionModal = true;
    }

    public function deletePrescription(): void
    {
        if ($this->deletingPrescriptionId) {
            $prescription = Prescription::findOrFail($this->deletingPrescriptionId);
            $prescription->delete();
            session()->flash('success', 'Ordonnance archivée.');
            $this->client->load('prescriptions', 'latestPrescription');
        }

        $this->showDeletePrescriptionModal = false;
        $this->deletingPrescriptionId = null;
    }

    public function resetPrescriptionForm(): void
    {
        $this->prescription_date = now()->format('Y-m-d');
        $this->prescribed_by = null;
        $this->prescription_notes = null;

        $this->od_sphere = null;
        $this->od_cylinder = null;
        $this->od_axis = null;
        $this->od_addition = null;

        $this->og_sphere = null;
        $this->og_cylinder = null;
        $this->og_axis = null;
        $this->og_addition = null;

        $this->pd = null;
        $this->pd_right = null;
        $this->pd_left = null;

        $this->prescription_file = null;
        $this->existing_prescription_file_path = null;
    }

    public function render()
    {
        $storeSetting = StoreSetting::current();

        $allOrders = $this->client->orders()->with(['items', 'payments', 'documents'])->get();
        $allPayments = $this->client->payments()->get();
        $allDocuments = $this->client->documents()->get();
        $allReminders = $this->client->reminders()->get();
        $allPrescriptions = $this->client->prescriptions()->get();

        $totalSpend = $this->client->totalLifetimeSpend();
        $totalPaid = $this->client->totalPaid();
        $totalDebt = $this->client->totalOutstandingDebt();

        return view('livewire.clients.client-show', [
            'storeSetting' => $storeSetting,
            'allOrders' => $allOrders,
            'allPayments' => $allPayments,
            'allDocuments' => $allDocuments,
            'allReminders' => $allReminders,
            'allPrescriptions' => $allPrescriptions,
            'totalSpend' => $totalSpend,
            'totalPaid' => $totalPaid,
            'totalDebt' => $totalDebt,
        ]);
    }
}
