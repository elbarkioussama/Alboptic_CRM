<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Settings\StoreSettingsManager;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StoreSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_store_settings_successfully(): void
    {
        $admin = User::factory()->admin()->create();
        StoreSetting::current();

        Livewire::actingAs($admin)
            ->test(StoreSettingsManager::class)
            ->set('business_name', 'ALBOPTIC MARRAKECH SARL')
            ->set('trade_name', 'ALBOPTIC Eyewear')
            ->set('address', '120 Avenue Mohammed V')
            ->set('city', 'Marrakech')
            ->set('phone', '+212 5 24 44 55 66')
            ->set('ice', '001234567890123')
            ->set('rc', '54321')
            ->set('tax_identifier', '987654')
            ->set('invoice_prefix', 'FAC-')
            ->set('order_prefix', 'BC-')
            ->set('default_tax_rate', 20.00)
            ->set('currency', 'MAD')
            ->call('save')
            ->assertHasNoErrors();

        $updated = StoreSetting::current();
        $this->assertEquals('ALBOPTIC MARRAKECH SARL', $updated->business_name);
        $this->assertEquals('120 Avenue Mohammed V', $updated->address);
        $this->assertEquals('001234567890123', $updated->ice);
    }

    public function test_store_settings_requires_valid_business_name_and_tax_rate(): void
    {
        $admin = User::factory()->admin()->create();
        StoreSetting::current();

        Livewire::actingAs($admin)
            ->test(StoreSettingsManager::class)
            ->set('business_name', '')
            ->set('default_tax_rate', 150)
            ->call('save')
            ->assertHasErrors(['business_name', 'default_tax_rate']);
    }
}
