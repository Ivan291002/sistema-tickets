<?php

use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

it('permite al creador del ticket agregar un comentario', function () {
    $usuario = User::factory()->create();
    $usuario->assignRole('usuario');
    $ticket = Ticket::factory()->for($usuario)->create();

    $this->actingAs($usuario)
        ->post(route('tickets.comentarios.store', $ticket), [
            'contenido' => 'Este es un comentario de seguimiento.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('comentarios', [
        'ticket_id' => $ticket->id,
        'user_id' => $usuario->id,
        'contenido' => 'Este es un comentario de seguimiento.',
    ]);
});

it('permite a un supervisor responder en cualquier ticket y actualiza el estado a en_proceso', function () {
    $supervisor = User::factory()->create();
    $supervisor->assignRole('supervisor');

    $usuario = User::factory()->create();
    $usuario->assignRole('usuario');
    $ticket = Ticket::factory()->for($usuario)->create(['estado' => 'abierto']);

    $this->actingAs($supervisor)
        ->post(route('tickets.comentarios.store', $ticket), [
            'contenido' => 'Estamos trabajando en tu solicitud.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('comentarios', [
        'ticket_id' => $ticket->id,
        'user_id' => $supervisor->id,
    ]);

    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'estado' => 'en_proceso',
    ]);
});

it('impide a un usuario ajeno comentar en el ticket de otro usuario', function () {
    $usuario1 = User::factory()->create();
    $usuario1->assignRole('usuario');

    $usuario2 = User::factory()->create();
    $usuario2->assignRole('usuario');

    $ticket = Ticket::factory()->for($usuario1)->create();

    $this->actingAs($usuario2)
        ->post(route('tickets.comentarios.store', $ticket), [
            'contenido' => 'Intento de comentario no autorizado.',
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('comentarios', [
        'contenido' => 'Intento de comentario no autorizado.',
    ]);
});

it('valida que el contenido del comentario no esté vacío', function () {
    $usuario = User::factory()->create();
    $usuario->assignRole('usuario');
    $ticket = Ticket::factory()->for($usuario)->create();

    $this->actingAs($usuario)
        ->post(route('tickets.comentarios.store', $ticket), [
            'contenido' => '',
        ])
        ->assertSessionHasErrors(['contenido']);
});
