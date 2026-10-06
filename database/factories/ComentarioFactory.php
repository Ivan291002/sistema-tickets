<?php

namespace Database\Factories;

use App\Models\Comentario;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comentario>
 */
class ComentarioFactory extends Factory
{
    protected $model = Comentario::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'contenido' => fake()->paragraph(),
        ];
    }
}
