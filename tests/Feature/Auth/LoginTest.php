<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Auth\Login;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_componente_de_login_rederiza()
    {
        Livewire::test(Login::class)
            ->assertStatus(200);
    }

    public function test_usuario_consegue_logar_com_credenciais_corretas()
    {
        $user = User::factory()->create([
            'email' => 'teste@exemplo.com.br',
            'password' => bcrypt('senha123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'teste@exemplo.com.br')
            ->set('password', 'senha123')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_usuario_nao_consegue_logar_com_credenciais_invalidas()
    {
        $user = User::factory()->create([
            'email' => 'teste@exemplo.com.br',
            'password' => bcrypt('senha123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'teste@exemplo.com.br')
            ->set('password', 'senha_errada')
            ->call('authenticate')
            ->assertHasErrors('email');

        $this->assertGuest();
    }

    public function test_email_e_senha_sao_obrigatorios()
    {
        Livewire::test(Login::class)
            ->set('email', '')
            ->set('password', '')
            ->call('authenticate')
            ->assertHasErrors(['email' => 'required', 'password' => 'required']);
    }
}
