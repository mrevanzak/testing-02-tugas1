<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_delete_product()
    {
        $barang = Barang::factory()->create();
        $admin = User::factory()->create(
            ['name' => 'admin',
                'email' => 'admin@gmail.com',
                'tipe_user' => '2',
                'password' => Hash::make('11111111'),
                'no_telp_user' => '082232319484',
            ]);

        $this->actingAs($admin)->deleteJson('/admin/delete', ['id_barang' => $barang->id_barang], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])->assertStatus(200)->assertJson(fn ($json) => $json->has('status'));

        $user = User::factory()->create();
        $this->actingAs($user)->get('/shop')->assertStatus(200)->assertSeeText('0 Products found');
    }
}
