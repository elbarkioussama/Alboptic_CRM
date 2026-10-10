<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_store_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/settings');
        $response->assertStatus(200);
        $response->assertSee('Paramètres du magasin');
    }

    public function test_staff_cannot_access_store_settings_and_gets_forbidden(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get('/settings');
        $response->assertStatus(403);
    }

    public function test_staff_can_access_dashboard_and_operational_modules(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get('/');
        $response->assertStatus(200);
        $response->assertSee('Tableau de bord');

        $response = $this->actingAs($staff)->get('/clients');
        $response->assertStatus(200);
    }
}
