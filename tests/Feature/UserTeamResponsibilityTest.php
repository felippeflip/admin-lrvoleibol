<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Time;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTeamResponsibilityTest extends TestCase
{
    // Using RefreshDatabase to ensure clean state. 
    // If this fails due to environment, we might need to change strategy.
    //use RefreshDatabase;

    public function test_can_assign_team_responsibility_on_create()
    {
        $admin = User::factory()->create();
        $time = Time::create([
            'tim_nome' => 'Test Team Create',
        ]);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'is_responsavel' => 1,
            'time_id' => $time->tim_id,
        ]);

        $response->assertRedirect(route('users.index'));

        $newUser = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($newUser);

        $this->assertEquals($newUser->id, $time->fresh()->tim_user_id);
    }

    public function test_can_assign_team_responsibility_on_edit()
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();
        $time = Time::create([
            'tim_nome' => 'Test Team Edit',
        ]);

        $response = $this->actingAs($admin)->put(route('users.update', $user->id), [
            'name' => 'Updated User',
            'email' => $user->email,
            'is_responsavel' => 1,
            'time_id' => $time->tim_id,
        ]);

        $response->assertRedirect(route('users.index'));

        $this->assertEquals($user->id, $time->fresh()->tim_user_id);
    }

    public function test_can_remove_team_responsibility_on_edit()
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();
        $time = Time::create([
            'tim_nome' => 'Test Team Remove',
            'tim_user_id' => $user->id,
        ]);

        $this->assertEquals($user->id, $time->fresh()->tim_user_id);

        $response = $this->actingAs($admin)->put(route('users.update', $user->id), [
            'name' => 'Updated User',
            'email' => $user->email,
            'is_responsavel' => 0, // Unchecked
        ]);

        $response->assertRedirect(route('users.index'));

        $this->assertNull($time->fresh()->tim_user_id);
    }

    public function test_changing_team_responsibility()
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();
        $time1 = Time::create(['tim_nome' => 'Team 1', 'tim_user_id' => $user->id]);
        $time2 = Time::create(['tim_nome' => 'Team 2']);

        $this->assertEquals($user->id, $time1->fresh()->tim_user_id);

        $response = $this->actingAs($admin)->put(route('users.update', $user->id), [
            'name' => 'Updated User',
            'email' => $user->email,
            'is_responsavel' => 1,
            'time_id' => $time2->tim_id,
        ]);

        $response->assertRedirect(route('users.index'));

        $this->assertNull($time1->fresh()->tim_user_id);
        $this->assertEquals($user->id, $time2->fresh()->tim_user_id);
    }
}
