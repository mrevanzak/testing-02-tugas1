<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_to_cart_without_login()
    {
        $response = $this->post('/shop/add-to-cart-ajax');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_add_to_cart_with_login()
    {
        $user = User::factory()->create();
        $barang = Barang::factory()->create();

        $this->actingAs($user)->postJson(route('shop.add-to-cart-ajax'), [
            'id_barang' => $barang->id_barang,
            'id_user' => $user->id_user,
            'nama' => $barang->nama_barang,
            'kuantitas' => 1,
            'harga' => $barang->harga_barang,
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])->assertStatus(200)->assertJsonPath('data.total_transaksi', $barang->harga_barang);

        $this->actingAs($user)->postJson(route('shop.add-to-cart-ajax'), [
            'id_barang' => $barang->id_barang,
            'id_user' => $user->id_user,
            'nama' => $barang->nama_barang,
            'kuantitas' => 2,
            'harga' => $barang->harga_barang,
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])->assertStatus(200)->assertJsonPath('data.total_transaksi', $barang->harga_barang * 2);
    }
}
