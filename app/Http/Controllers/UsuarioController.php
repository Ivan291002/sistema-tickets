<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('gestionar usuarios');

        $query = User::with('roles')
            ->when($request->filled('q'), function ($q) use ($request) {
                $search = $request->q;
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest();

        $usuarios = $query->paginate(15);
        $roles = Role::all();

        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    public function update(Request $request, User $usuario)
    {
        $this->authorize('gestionar usuarios');

        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        // Prevenir que un administrador se quite a sí mismo el rol de admin si es el único
        if ($usuario->id === $request->user()->id && $request->role !== 'admin') {
            return back()->with('error', 'No puedes cambiar tu propio rol de administrador.');
        }

        $usuario->syncRoles([$request->role]);

        return back()->with('success', "Rol de '{$usuario->name}' actualizado a '{$request->role}'.");
    }
}
