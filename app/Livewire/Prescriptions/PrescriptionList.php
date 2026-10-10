<?php

declare(strict_types=1);

namespace App\Livewire\Prescriptions;

use App\Models\Client;
use App\Models\Prescription;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Ordonnances & Réfractions — ALBOPTIC CRM')]
class PrescriptionList extends Component
{
    use WithPagination, WithFileUploads;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public ?int $clientIdFilter = null;

    // Modal state
    public bool $showModal = false;
    public ?int $editingPrescriptionId = null;

    // Detail modal state
    public bool $showDetailModal = false;
    public ?Prescription $viewingPrescription = null;

    // Delete modal state
    public bool $showDeleteModal = false;
    public ?int $deletingPrescriptionId = null;

    // Form fields
    public ?int $client_id = null;
    public string $client_search = '';
    public string $prescription_date = '';
    public ?string $prescribed_by = null;
    public ?string $notes = null;

    // OD (Right eye)
    public ?string $od_sphere = null;
    public ?string $od_cylinder = null;
    public ?string $od_axis = null;
    public ?string $od_addition = null;

    // OG (Left eye)
    public ?string $og_sphere = null;
    public ?string $og_cylinder = null;
    public ?string $og_axis = null;
    public ?string $og_addition = null;

    // Pupillary Distance
    public ?string $pd = null;
    public ?string $pd_right = null;
    public ?string $pd_left = null;

    // File upload
    public $prescription_file = null;
    public ?string $existing_file_path = null;

    public function mount(): void
    {
        $this->prescription_date = now()->format('Y-m-d');

        if ($this->clientIdFilter) {
            $this->client_id = $this->clientIdFilter;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedClientIdFilter(): void
    {
        $this->resetPage();
    }

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

    protected function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'prescription_date' => ['required', 'date'],
            'prescribed_by' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:2000'],

            // OD
            'od_sphere' => ['nullable', 'numeric', 'between:-30,30'],
            'od_cylinder' => ['nullable', 'numeric', 'between:-20,20'],
            'od_axis' => ['nullable', 'integer', 'between:0,180'],
            'od_addition' => ['nullable', 'numeric', 'min:0', 'max:10'],

            // OG
            'og_sphere' => ['nullable', 'numeric', 'between:-30,30'],
            'og_cylinder' => ['nullable', 'numeric', 'between:-20,20'],
            'og_axis' => ['nullable', 'integer', 'between:0,180'],
            'og_addition' => ['nullable', 'numeric', 'min:0', 'max:10'],

            // PD
            'pd' => ['nullable', 'numeric', 'between:20,90'],
            'pd_right' => ['nullable', 'numeric', 'between:10,50'],
            'pd_left' => ['nullable', 'numeric', 'between:10,50'],

            // File
            'prescription_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    protected function messages(): array
    {
        return [
            'client_id.required' => 'Veuillez sélectionner un patient / client.',
            'client_id.exists' => 'Le client sélectionné est invalide.',
            'prescription_date.required' => 'La date de l\'ordonnance est obligatoire.',
            'od_axis.between' => 'L\'axe de l\'œil droit doit être compris entre 0° et 180°.',
            'og_axis.between' => 'L\'axe de l\'œil gauche doit être compris entre 0° et 180°.',
            'od_addition.min' => 'L\'addition OD doit être une valeur positive.',
            'og_addition.min' => 'L\'addition OG doit être une valeur positive.',
            'prescription_file.mimes' => 'Le document doit être au format PDF, JPG, JPEG ou PNG.',
            'prescription_file.max' => 'La taille du document ne doit pas dépasser 10 Mo.',
        ];
    }

    public function openCreateModal(?int $preselectedClientId = null): void
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editingPrescriptionId = null;

        if ($preselectedClientId) {
            $this->client_id = $preselectedClientId;
            $client = Client::find($preselectedClientId);
            if ($client) {
                $this->client_search = $client->full_name;
            }
        } elseif ($this->clientIdFilter) {
            $this->client_id = $this->clientIdFilter;
            $client = Client::find($this->clientIdFilter);
            if ($client) {
                $this->client_search = $client->full_name;
            }
        }

        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $prescription = Prescription::with('client')->findOrFail($id);

        $this->editingPrescriptionId = $prescription->id;
        $this->client_id = $prescription->client_id;
        $this->client_search = $prescription->client->full_name ?? '';
        $this->prescription_date = $prescription->prescription_date->format('Y-m-d');
        $this->prescribed_by = $prescription->prescribed_by;
        $this->notes = $prescription->notes;

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

        $this->existing_file_path = $prescription->prescription_file_path;
        $this->prescription_file = null;

        $this->showModal = true;
    }

    public function viewDetails(int $id): void
    {
        $this->viewingPrescription = Prescription::with(['client', 'orders'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function savePrescription(): void
    {
        $validated = $this->validate();

        $data = [
            'client_id' => $validated['client_id'],
            'prescription_date' => $validated['prescription_date'],
            'prescribed_by' => $validated['prescribed_by'] ? trim($validated['prescribed_by']) : null,
            'notes' => $validated['notes'] ? trim($validated['notes']) : null,

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

        // Handle secure file upload
        if ($this->prescription_file) {
            $path = $this->prescription_file->store('prescriptions', 'local');
            $data['prescription_file_path'] = $path;
        }

        if ($this->editingPrescriptionId) {
            $prescription = Prescription::findOrFail($this->editingPrescriptionId);
            $prescription->update($data);
            session()->flash('success', "Ordonnance du {$prescription->prescription_date->format('d/m/Y')} mise à jour avec succès.");
        } else {
            $prescription = Prescription::create($data);
            session()->flash('success', "Nouvelle ordonnance enregistrée avec succès pour {$prescription->client->full_name}.");
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function deleteAttachedFile(): void
    {
        if ($this->editingPrescriptionId) {
            $prescription = Prescription::findOrFail($this->editingPrescriptionId);
            if ($prescription->prescription_file_path && Storage::disk('local')->exists($prescription->prescription_file_path)) {
                Storage::disk('local')->delete($prescription->prescription_file_path);
            }
            $prescription->update(['prescription_file_path' => null]);
            $this->existing_file_path = null;
            session()->flash('success', 'Document supprimé de l\'ordonnance.');
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingPrescriptionId = $id;
        $this->showDeleteModal = true;
    }

    public function deletePrescription(): void
    {
        if ($this->deletingPrescriptionId) {
            $prescription = Prescription::findOrFail($this->deletingPrescriptionId);
            $prescription->delete();
            session()->flash('success', 'L\'ordonnance a été archivée avec succès.');
        }

        $this->showDeleteModal = false;
        $this->deletingPrescriptionId = null;
    }

    public function resetForm(): void
    {
        $this->client_id = null;
        $this->client_search = '';
        $this->prescription_date = now()->format('Y-m-d');
        $this->prescribed_by = null;
        $this->notes = null;

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
        $this->existing_file_path = null;
    }

    public function selectClient(int $id, string $name): void
    {
        $this->client_id = $id;
        $this->client_search = $name;
    }

    public function render()
    {
        $storeSetting = StoreSetting::current();

        $query = Prescription::query()
            ->with(['client', 'orders'])
            ->search($this->search);

        if ($this->clientIdFilter) {
            $query->where('client_id', $this->clientIdFilter);
        }

        $prescriptions = $query->orderByDesc('prescription_date')
            ->orderByDesc('id')
            ->paginate(15);

        // Searchable clients for the create/edit modal
        $clientSuggestions = [];
        if (strlen(trim($this->client_search)) >= 2 && !$this->client_id) {
            $clientSuggestions = Client::search($this->client_search)->limit(6)->get();
        }

        return view('livewire.prescriptions.prescription-list', [
            'prescriptions' => $prescriptions,
            'storeSetting' => $storeSetting,
            'clientSuggestions' => $clientSuggestions,
            'activeFilterClient' => $this->clientIdFilter ? Client::find($this->clientIdFilter) : null,
        ]);
    }
}
