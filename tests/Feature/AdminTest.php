<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\User;
use Database\Seeders\BarangSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

    public function test_add_product()
    {
        $admin = User::factory()->create(
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'tipe_user' => '2',
                'password' => Hash::make('11111111'),
                'no_telp_user' => '082232319484',
            ]
        );
        $this->seed(BarangSeeder::class);

        $this->actingAs($admin)->postJson('/admin/create-product', [
            'kategori' => fake()->randomElement(['Food', 'Drink', 'Snack']),
            'nama' => 'percobaan',
            'price' => fake()->randomNumber(5),
            'berat' => fake()->randomNumber(3),
            'stok' => fake()->randomNumber(2),
            'deskripsi' => fake()->text,
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])->assertStatus(200);

        $this->assertDatabaseHas('barangs', [
            'nama_barang' => 'percobaan',
        ]);
    }

    public function test_edit_product()
    {
        $barang = Barang::factory()->create();
        $admin = User::factory()->create(
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'tipe_user' => '2',
                'password' => Hash::make('11111111'),
                'no_telp_user' => '082232319484',
            ]
        );

        $this->actingAs($admin)->postJson('/admin/update-product', [
            'id_barang' => $barang->id_barang,
            'kategori' => fake()->randomElement(['Food', 'Drink', 'Snack']),
            'nama' => 'teredit',
            'price' => fake()->randomNumber(5),
            'berat' => fake()->randomNumber(3),
            'stok' => fake()->randomNumber(2),
            'deskripsi' => fake()->text,
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])->assertStatus(200);

        $this->assertDatabaseHas('barangs', [
            'nama_barang' => 'teredit',
        ]);

    }

    public function test_delete_product()
    {
        $barang = Barang::factory()->create();
        $admin = User::factory()->create(
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'tipe_user' => '2',
                'password' => Hash::make('11111111'),
                'no_telp_user' => '082232319484',
            ]
        );

        $this->actingAs($admin)->deleteJson('/admin/delete', ['id_barang' => $barang->id_barang], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])->assertStatus(200)->assertJson(fn ($json) => $json->has('status'));

        $user = User::factory()->create();
        $this->actingAs($user)->get('/shop')->assertStatus(200)->assertSeeText('0 Products found');
    }

    public function test_receive_transaction()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'tipe_user' => '2',
                'password' => Hash::make('11111111'),
                'no_telp_user' => '082232319484',
            ]
        );
        $barang = Barang::factory()->create();

        $this->actingAs($user)->postJson(route('shop.add-to-cart-ajax'), [
            'id_barang' => $barang->id_barang,
            'id_user' => $user->id_user,
            'nama' => $barang->nama_barang,
            'kuantitas' => 1,
            'harga' => $barang->harga_barang,
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])->assertStatus(200)->assertJsonPath('data.total_transaksi', $barang->harga_barang);

        $this->actingAs($user)->postJson(route('shop.checkout-payment-ajax'), [
            'address' => 'keputih, surabaya',
            'id_transaksi' => '1',
            'payment' => 'debit',
            'latitude' => '1',
            'longitude' => '1',
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])->assertStatus(200)->assertJson(fn ($json) => $json->has('data'));

        $this->actingAs($admin)->get(route('admin.transaksi'))->assertStatus(200)->assertViewIs('admin.transaksi')->assertSeeText($user->name);
    }

    public function test_edit_transaction_status()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'tipe_user' => '2',
                'password' => Hash::make('11111111'),
                'no_telp_user' => '082232319484',
            ]
        );
        Transaksi::factory()->create(
            [
                'id_transaksi' => 1,
                'metode_transaksi' => 'debit',
                'total_transaksi' => 10000,
                'latitude' => -7.2930927,
                'longitude' => 112.7977454,
                'status_transaksi' => 1,
                'alamat_dikirim' => fake()->address,
                'id_user' => $user->id_user,
            ]
        );

        $this->actingAs($admin)->putJson('/admin/change-status-done', ['id_transaksi' => 1], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])->assertStatus(200)->assertJson(['status' => true]);
    }
}
