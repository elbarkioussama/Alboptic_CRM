<?php

declare(strict_types=1);

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Models\StoreSetting;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Répertoire des Clients — ALBOPTIC CRM')]
class ClientList extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $filterDebt = 'all'; // 'all', 'debt_only'

    public bool $showModal = false;
    public ?int $editingClientId = null;

    // Form fields
    public string $first_name = '';
    public string $last_name = '';
    public string $phone = '';
    public ?string $email = null;
    public ?string $gender = null;
    public ?string $date_of_birth = null;
    public ?string $address = null;
    public string $city = 'Marrakech';
    public ?string $notes = null;

    public bool $showDeleteModal = false;
    public ?int $deletingClientId = null;
    public ?string $deleteWarning = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDebt(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['nullable', 'string', 'in:male,female,child'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editingClientId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $client = Client::findOrFail($id);
        $this->editingClientId = $client->id;
        $this->first_name = $client->first_name;
        $this->last_name = $client->last_name;
        $this->phone = $client->phone;
        $this->email = $client->email;
        $this->gender = $client->gender;
        $this->date_of_birth = $client->date_of_birth?->format('Y-m-d');
        $this->address = $client->address;
        $this->city = $client->city ?? 'Marrakech';
        $this->notes = $client->notes;

        $this->showModal = true;
    }

    public function saveClient(): void
    {
        $validated = $this->validate();

        // Normalize phone number (trim and remove extra whitespace)
        $validated['phone'] = trim($validated['phone']);

        if ($this->editingClientId) {
            $client = Client::findOrFail($this->editingClientId);
            $client->update($validated);
            session()->flash('success', "Fiche client de {$client->full_name} mise à jour avec succès.");
        } else {
            $client = Client::create($validated);
            session()->flash('success', "Nouveau client {$client->full_name} enregistré avec succès.");
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $client = Client::with('orders')->findOrFail($id);
        $this->deletingClientId = $client->id;

        if (! $client->canBeSafelyDeleted()) {
            $this->deleteWarning = "Attention : Ce client possède des commandes ou un solde impayé en cours. La suppression archivera le dossier mais préservera la traçabilité comptable.";
        } else {
            $this->deleteWarning = null;
        }

        $this->showDeleteModal = true;
    }

    public function deleteClient(): void
    {
        if ($this->deletingClientId) {
            $client = Client::findOrFail($this->deletingClientId);
            $name = $client->full_name;
            $client->delete();

            session()->flash('success', "Le dossier client de {$name} a été supprimé.");
        }

        $this->showDeleteModal = false;
        $this->deletingClientId = null;
    }

    public function resetForm(): void
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->phone = '';
        $this->email = null;
        $this->gender = null;
        $this->date_of_birth = null;
        $this->address = null;
        $this->city = 'Marrakech';
        $this->notes = null;
    }

    public function render()
    {
        $storeSetting = StoreSetting::current();

        $query = Client::query()
            ->search($this->search)
            ->withCount('orders')
            ->with(['latestOrder']);

        if ($this->filterDebt === 'debt_only') {
            $query->whereHas('orders', function ($q) {
                $q->where('remaining_amount', '>', 0)
                    ->whereNotIn('status', ['cancelled']);
            });
        }

        $clients = $query->orderByDesc('id')->paginate(12);

        return view('livewire.clients.client-list', [
            'clients' => $clients,
            'storeSetting' => $storeSetting,
        ]);
    }
}
