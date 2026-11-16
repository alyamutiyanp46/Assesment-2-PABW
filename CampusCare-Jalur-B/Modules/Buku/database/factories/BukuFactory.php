<?php

namespace Modules\Buku\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Buku\Models\Buku;

/**
 * @extends Factory<Buku>
 */
class BukuFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Buku::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_buku' => fake()->unique()->bothify('BK########'),
            'judul' => fake()->sentence(3),
            'penulis' => fake()->name(),
            'kategori' => fake()->randomElement(['Teknologi', 'Sastra', 'Sejarah', 'Pendidikan']),
            'stok' => fake()->numberBetween(1, 50),
        ];
    }
}
