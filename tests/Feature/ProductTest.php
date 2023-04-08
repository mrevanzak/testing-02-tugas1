<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\User;
use Database\Seeders\BarangSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_available_products_can_be_retrieved()
    {
        $product = Barang::factory()->create();
        $user = User::factory()->create();
        $this->seed(UserSeeder::class);

        $this->actingAs($user)->getJson(route(
            'shop.detail',
            ['id' => $product->id_barang]
        ))->assertStatus(200)->assertViewIs('shop.detail')->assertViewHas('data', fn ($data) => $data['produk']['stok'] === $product->stok_barang);
    }

    public function test_search_product()
    {
        $user = User::factory()->create();
        $this->seed(BarangSeeder::class);

        $this->actingAs($user)->get('/shop/search-ajax?page=1&search='.'ngawur',
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        )->assertStatus(200)->assertSeeText('0 Products found');
    }
}
