<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Categoria>
 */
class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->randomElement(['Hardware', 'Software', 'Redes', 'Soporte General', 'Facturación', 'Seguridad', 'Accesos']),
            'color' => fake()->randomElement(['blue', 'emerald', 'amber', 'purple', 'rose', 'cyan']),
            'descripcion' => fake()->sentence(),
        ];
    }
}
