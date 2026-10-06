<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

it('permite al administrador ver el listado de usuarios', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get(route('usuarios.index'))
        ->assertOk()
        ->assertSee('Gestión de Usuarios');
});

it('prohíbe a un usuario normal acceder a la gestión de usuarios', function () {
    $usuario = User::factory()->create();
    $usuario->assignRole('usuario');

    $this->actingAs($usuario)
        ->get(route('usuarios.index'))
        ->assertForbidden();
});

it('permite al administrador cambiar el rol de un usuario', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $usuario = User::factory()->create();
    $usuario->assignRole('usuario');

    $this->actingAs($admin)
        ->put(route('usuarios.update', $usuario), [
            'role' => 'supervisor',
        ])
        ->assertRedirect();

    expect($usuario->fresh()->hasRole('supervisor'))->toBeTrue();
});

it('impide que un administrador se quite su propio rol de admin', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->put(route('usuarios.update', $admin), [
            'role' => 'usuario',
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($admin->fresh()->hasRole('admin'))->toBeTrue();
});
