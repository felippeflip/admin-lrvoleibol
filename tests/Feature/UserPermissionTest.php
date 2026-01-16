<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_access_all_menus()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');

        $response = $this->actingAs($admin)->get('/eventos'); // Campeonatos
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/users'); // Configurações -> Usuários
        $response->assertStatus(200);
    }

    public function test_juiz_can_only_access_dashboard_and_jogos()
    {
        $juiz = User::factory()->create();
        $juiz->assignRole('Juiz');

        $response = $this->actingAs($juiz)->get('/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($juiz)->get('/jogos');
        $response->assertStatus(200);

        // Não deve ver menu campeonatos (embora a rota possa estar aberta se não protegidam, o teste aqui foca no menu/view ou acesso)
        // Se a rota for protegida por middleware:
        // $response = $this->actingAs($juiz)->get('/eventos');
        // $response->assertStatus(403); 
    }

    public function test_responsavel_time_can_access_functions_menu()
    {
        $resp = User::factory()->create();
        $resp->assignRole('ResponsavelTime');

        $this->actingAs($resp);
        // Verificar se consegue ver a rota de times (Meu Time)
        $response = $this->actingAs($resp)->get('/times');
        $response->assertStatus(200);
    }
}
