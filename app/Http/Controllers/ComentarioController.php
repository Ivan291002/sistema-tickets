<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Ticket;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        // El usuario solo puede comentar si es el dueño del ticket o si tiene rol de supervisor/admin
        if ($user->hasRole('usuario') && $ticket->user_id !== $user->id) {
            abort(403, 'No tienes permiso para comentar en este ticket.');
        }

        $datos = $request->validate([
            'contenido' => ['required', 'string', 'max:5000'],
        ]);

        Comentario::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'contenido' => $datos['contenido'],
        ]);

        // Si el ticket estaba abierto y un supervisor/admin responde, actualizar a en_proceso
        if ($ticket->estado === 'abierto' && ($user->hasRole('supervisor') || $user->hasRole('admin'))) {
            $ticket->update(['estado' => 'en_proceso']);
        }

        return back()->with('success', 'Comentario añadido con éxito.');
    }
}
