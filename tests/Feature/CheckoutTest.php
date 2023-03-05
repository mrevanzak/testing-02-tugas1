<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_without_login()
    {
        $response = $this->get('/shop/checkout');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_checkout_with_login()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route('shop.checkout-payment-ajax'), [
            'address' => 'keputih, surabaya',
            'id_transaksi' => '1',
            'payment' => 'debit',
            'latitude' => '1',
            'longitude' => '1',
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])->assertStatus(200)->assertJson(fn ($json) => $json->has('data'));
    }
}
