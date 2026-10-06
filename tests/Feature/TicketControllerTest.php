<?php

use App\Models\Categoria;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

it('redirects guests away from the tickets index', function () {
    $this->get(route('tickets.index'))->assertRedirect(route('login'));
});

it('renders the tickets index for an authenticated user', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)->get(route('tickets.index'))->assertOk();
});

it('renders the create form for an authenticated user', function () {
    $user = User::factory()->create();
    $user->assignRole('usuario');

    $this->actingAs($user)->get(route('tickets.create'))
        ->assertOk()
        ->assertSee('Nuevo ticket');
});

it('renders the edit form for a supervisor', function () {
    $supervisor = User::factory()->create();
    $supervisor->assignRole('supervisor');
    $ticket = Ticket::factory()->for($supervisor)->create();

    $this->actingAs($supervisor)->get(route('tickets.edit', $ticket))
        ->assertOk()
        ->assertSee($ticket->titulo);
});

it('shows only the tickets of the user with the usuario role', function () {
    $usuario = User::factory()->create();
    $usuario->assignRole('usuario');
    $other = User::factory()->create();

    $own = Ticket::factory()->for($usuario)->create();
    $foreign = Ticket::factory()->for($other)->create();

    $this->actingAs($usuario)->get(route('tickets.index'))
        ->assertOk()
        ->assertSee($own->titulo)
        ->assertDontSee($foreign->titulo);
});

it('shows all tickets to a supervisor', function () {
    $supervisor = User::factory()->create();
    $supervisor->assignRole('supervisor');
    $owner = User::factory()->create();

    $own = Ticket::factory()->for($supervisor)->create();
    $foreign = Ticket::factory()->for($owner)->create();

    $this->actingAs($supervisor)->get(route('tickets.index'))
        ->assertOk()
        ->assertSee($own->titulo)
        ->assertSee($foreign->titulo);
});

it('lets an authenticated user create a ticket', function () {
    $user = User::factory()->create();
    $user->assignRole('usuario');
    $this->actingAs($user);

    $this->post(route('tickets.store'), [
        'titulo' => 'No funciona la impresora',
        'descripcion' => 'Se queda sin imprimir.',
    ])->assertRedirect(route('tickets.index'));

    $this->assertDatabaseHas('tickets', [
        'user_id' => $user->id,
        'titulo' => 'No funciona la impresora',
        'estado' => 'abierto',
    ]);
});

it('validates the title and description when creating a ticket', function () {
    $user = User::factory()->create();
    $user->assignRole('usuario');
    $this->actingAs($user);

    $this->post(route('tickets.store'), [
        'titulo' => '',
        'descripcion' => '',
    ])->assertSessionHasErrors(['titulo', 'descripcion']);
});

it('forbids a user without the editar tickets permission from editing', function () {
    $usuario = User::factory()->create();
    $usuario->assignRole('usuario');
    $ticket = Ticket::factory()->create();

    $this->actingAs($usuario)->get(route('tickets.edit', $ticket))->assertForbidden();
});

it('lets a supervisor update a ticket', function () {
    $supervisor = User::factory()->create();
    $supervisor->assignRole('supervisor');
    $ticket = Ticket::factory()->for($supervisor)->create();

    $this->actingAs($supervisor)->put(route('tickets.update', $ticket), [
        'titulo' => 'Ticket actualizado',
        'descripcion' => 'Nueva descripción.',
    ])->assertRedirect(route('tickets.index'));

    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'titulo' => 'Ticket actualizado',
    ]);
});

it('lets a supervisor close an open ticket', function () {
    $supervisor = User::factory()->create();
    $supervisor->assignRole('supervisor');
    $ticket = Ticket::factory()->for($supervisor)->create();

    $this->actingAs($supervisor)->patch(route('tickets.cerrar', $ticket))->assertRedirect(route('tickets.index'));

    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'estado' => 'cerrado',
    ]);
});

it('forbids a usuario from seeing a foreign ticket', function () {
    $usuario = User::factory()->create();
    $usuario->assignRole('usuario');
    $owner = User::factory()->create();
    $ticket = Ticket::factory()->for($owner)->create();

    $this->actingAs($usuario)->get(route('tickets.show', $ticket))->assertForbidden();
});

it('shows a ticket to its owner', function () {
    $usuario = User::factory()->create();
    $usuario->assignRole('usuario');
    $ticket = Ticket::factory()->for($usuario)->create();

    $this->actingAs($usuario)->get(route('tickets.show', $ticket))
        ->assertOk()
        ->assertSee($ticket->titulo);
});

it('permite crear un ticket con prioridad y categoría', function () {
    $usuario = User::factory()->create();
    $usuario->assignRole('usuario');
    $categoria = Categoria::factory()->create();

    $this->actingAs($usuario)->post(route('tickets.store'), [
        'titulo' => 'Problema con red',
        'descripcion' => 'No hay conexión en la sala 3.',
        'prioridad' => 'alta',
        'categoria_id' => $categoria->id,
    ])->assertRedirect(route('tickets.index'));

    $this->assertDatabaseHas('tickets', [
        'titulo' => 'Problema con red',
        'prioridad' => 'alta',
        'categoria_id' => $categoria->id,
    ]);
});

it('permite cambiar el estado de un ticket', function () {
    $supervisor = User::factory()->create();
    $supervisor->assignRole('supervisor');
    $ticket = Ticket::factory()->create(['estado' => 'abierto']);

    $this->actingAs($supervisor)->patch(route('tickets.estado', $ticket), [
        'estado' => 'en_proceso',
    ])->assertRedirect();

    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'estado' => 'en_proceso',
    ]);
});

it('permite asignar un responsable a un ticket', function () {
    $supervisor = User::factory()->create();
    $supervisor->assignRole('supervisor');
    $ticket = Ticket::factory()->create();

    $this->actingAs($supervisor)->patch(route('tickets.asignar', $ticket), [
        'asignado_a' => $supervisor->id,
    ])->assertRedirect();

    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'asignado_a' => $supervisor->id,
    ]);
});

it('filtra tickets por estado y por texto de búsqueda', function () {
    $supervisor = User::factory()->create();
    $supervisor->assignRole('supervisor');

    $ticketAbierto = Ticket::factory()->create(['titulo' => 'Servidor caído', 'estado' => 'abierto']);
    $ticketCerrado = Ticket::factory()->create(['titulo' => 'Mouse roto', 'estado' => 'cerrado']);

    $this->actingAs($supervisor)->get(route('tickets.index', ['estado' => 'abierto']))
        ->assertOk()
        ->assertSee($ticketAbierto->titulo)
        ->assertDontSee($ticketCerrado->titulo);

    $this->actingAs($supervisor)->get(route('tickets.index', ['q' => 'Servidor']))
        ->assertOk()
        ->assertSee($ticketAbierto->titulo)
        ->assertDontSee($ticketCerrado->titulo);
});
