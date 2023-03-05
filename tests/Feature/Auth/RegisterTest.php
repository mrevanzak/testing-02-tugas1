<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_register()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'name' => 'test']);
    }

    public function test_register_email_already_registered()
    {
        User::factory()->create([
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'no_telp_user' => '082232319484',
        ]);

        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors('email');
    }
}
