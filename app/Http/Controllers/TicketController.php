<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query()
            ->with(['user', 'categoria', 'asignado'])
            ->when($request->user()->hasRole('usuario'), fn ($q) => $q->where('user_id', $request->user()->id))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->estado))
            ->when($request->filled('prioridad'), fn ($q) => $q->where('prioridad', $request->prioridad))
            ->when($request->filled('categoria_id'), fn ($q) => $q->where('categoria_id', $request->categoria_id))
            ->when($request->filled('q'), function ($q) use ($request) {
                $search = $request->q;
                $q->where(function ($sub) use ($search) {
                    $sub->where('titulo', 'like', "%{$search}%")
                        ->orWhere('descripcion', 'like', "%{$search}%")
                        ->orWhere('id', $search);
                });
            })
            ->latest();

        $tickets = $query->get();
        $categorias = Categoria::orderBy('nombre')->get();

        return view('tickets.index', compact('tickets', 'categorias'));
    }

    public function create()
    {
        $this->authorize('crear tickets');

        $categorias = Categoria::orderBy('nombre')->get();

        return view('tickets.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $this->authorize('crear tickets');

        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'prioridad' => ['nullable', 'string', 'in:baja,media,alta,urgente'],
            'categoria_id' => ['nullable', 'exists:categorias,id'],
        ]);

        Ticket::create([
            'user_id' => $request->user()->id,
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'prioridad' => $datos['prioridad'] ?? 'media',
            'categoria_id' => $datos['categoria_id'] ?? null,
            'estado' => 'abierto',
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket creado correctamente.');
    }

    public function show(Request $request, Ticket $ticket)
    {
        if ($request->user()->hasRole('usuario') && $ticket->user_id !== $request->user()->id) {
            abort(403);
        }

        $ticket->load(['user', 'categoria', 'asignado', 'comentarios.user']);
        $supervisores = User::role(['admin', 'supervisor'])->orderBy('name')->get();

        return view('tickets.show', compact('ticket', 'supervisores'));
    }

    public function edit(Ticket $ticket)
    {
        $this->authorize('editar tickets');

        $ticket->load(['categoria', 'asignado']);
        $categorias = Categoria::orderBy('nombre')->get();
        $supervisores = User::role(['admin', 'supervisor'])->orderBy('name')->get();

        return view('tickets.edit', compact('ticket', 'categorias', 'supervisores'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('editar tickets');

        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'prioridad' => ['nullable', 'string', 'in:baja,media,alta,urgente'],
            'categoria_id' => ['nullable', 'exists:categorias,id'],
            'asignado_a' => ['nullable', 'exists:users,id'],
            'estado' => ['nullable', 'string', 'in:abierto,en_proceso,cerrado'],
        ]);

        $ticket->update($datos);

        return redirect()->route('tickets.index')->with('success', 'Ticket actualizado correctamente.');
    }

    public function cambiarEstado(Request $request, Ticket $ticket)
    {
        $this->authorize('cerrar tickets');

        $datos = $request->validate([
            'estado' => ['required', 'string', 'in:abierto,en_proceso,cerrado'],
        ]);

        $ticket->update(['estado' => $datos['estado']]);

        return back()->with('success', "Estado del ticket actualizado a '{$datos['estado']}'.");
    }

    public function asignar(Request $request, Ticket $ticket)
    {
        $this->authorize('editar tickets');

        $datos = $request->validate([
            'asignado_a' => ['nullable', 'exists:users,id'],
        ]);

        $ticket->update(['asignado_a' => $datos['asignado_a'] ?? null]);

        return back()->with('success', 'Responsable del ticket actualizado.');
    }

    public function cerrar(Ticket $ticket)
    {
        $this->authorize('cerrar tickets');

        $ticket->update(['estado' => 'cerrado']);

        return redirect()->route('tickets.index')->with('success', 'Ticket cerrado correctamente.');
    }
}
