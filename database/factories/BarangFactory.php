<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Barang>
 */
class BarangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'stok_barang' => 11,
            'harga_barang' => 15000,
            'nama_barang' => 'Gula',
            'deskripsi_barang' => 'Gula 1 kg',
            'nama_kategori' => 'Food',
            'gambar_barang' => 'images/products/1.jpg',
            'berat_barang' => 1000, // bisa dalam gram
            'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
        ];
    }
}
