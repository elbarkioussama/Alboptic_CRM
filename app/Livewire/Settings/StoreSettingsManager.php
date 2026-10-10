<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Models\StoreSetting;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Paramètres du magasin — ALBOPTIC CRM')]
class StoreSettingsManager extends Component
{
    public string $business_name = '';
    public ?string $trade_name = null;
    public ?string $address = null;
    public ?string $city = 'Marrakech';
    public ?string $phone = null;
    public ?string $email = null;
    public ?string $ice = null;
    public ?string $rc = null;
    public ?string $tax_identifier = null;
    public string $invoice_prefix = 'FAC-';
    public string $order_prefix = 'BC-';
    public float $default_tax_rate = 20.00;
    public string $currency = 'MAD';

    public function mount(): void
    {
        Gate::authorize('manage-settings');

        $setting = StoreSetting::current();

        $this->business_name = $setting->business_name;
        $this->trade_name = $setting->trade_name;
        $this->address = $setting->address;
        $this->city = $setting->city;
        $this->phone = $setting->phone;
        $this->email = $setting->email;
        $this->ice = $setting->ice;
        $this->rc = $setting->rc;
        $this->tax_identifier = $setting->tax_identifier;
        $this->invoice_prefix = $setting->invoice_prefix;
        $this->order_prefix = $setting->order_prefix;
        $this->default_tax_rate = (float) $setting->default_tax_rate;
        $this->currency = $setting->currency;
    }

    protected function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'trade_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'ice' => ['nullable', 'string', 'max:50'],
            'rc' => ['nullable', 'string', 'max:50'],
            'tax_identifier' => ['nullable', 'string', 'max:50'],
            'invoice_prefix' => ['required', 'string', 'max:20'],
            'order_prefix' => ['required', 'string', 'max:20'],
            'default_tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'currency' => ['required', 'string', 'max:10'],
        ];
    }

    public function save(): void
    {
        Gate::authorize('manage-settings');

        $validated = $this->validate();

        $setting = StoreSetting::current();
        $setting->update($validated);

        session()->flash('success', 'Les paramètres du magasin ont été enregistrés avec succès.');
    }

    public function render()
    {
        return view('livewire.settings.store-settings-manager');
    }
}
