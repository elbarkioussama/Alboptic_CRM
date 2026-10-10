<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('ALBOPTIC CRM');
    }

    public function test_users_can_authenticate_using_the_login_component(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@alboptic.ma',
            'password' => 'secret123',
            'is_active' => true,
        ]);

        Livewire::test(Login::class)
            ->set('email', 'admin@alboptic.ma')
            ->set('password', 'secret123')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'admin@alboptic.ma',
            'password' => 'secret123',
        ]);

        Livewire::test(Login::class)
            ->set('email', 'admin@alboptic.ma')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_inactive_users_cannot_authenticate(): void
    {
        User::factory()->create([
            'email' => 'staff@alboptic.ma',
            'password' => 'secret123',
            'is_active' => false,
        ]);

        Livewire::test(Login::class)
            ->set('email', 'staff@alboptic.ma')
            ->set('password', 'secret123')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');

        $response = $this->get('/settings');
        $response->assertRedirect('/login');
    }
}
