<?php

use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UsuarioController;
use App\Models\Ticket;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $abiertos = Ticket::where('estado', 'abierto')->count();
        $enProceso = Ticket::where('estado', 'en_proceso')->count();
        $cerrados = Ticket::where('estado', 'cerrado')->count();
        $total = Ticket::count();

        $user = auth()->user();
        $misTickets = $user->hasRole('usuario')
            ? Ticket::where('user_id', $user->id)->count()
            : Ticket::where('asignado_a', $user->id)->count();

        $recientes = Ticket::with(['user', 'categoria', 'asignado'])
            ->when($user->hasRole('usuario'), fn ($q) => $q->where('user_id', $user->id))
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('abiertos', 'enProceso', 'cerrados', 'total', 'misTickets', 'recientes'));
    })->name('dashboard');

    Route::resource('tickets', TicketController::class)->except(['destroy']);
    Route::patch('tickets/{ticket}/cerrar', [TicketController::class, 'cerrar'])->name('tickets.cerrar');
    Route::patch('tickets/{ticket}/estado', [TicketController::class, 'cambiarEstado'])->name('tickets.estado');
    Route::patch('tickets/{ticket}/asignar', [TicketController::class, 'asignar'])->name('tickets.asignar');
    Route::post('tickets/{ticket}/comentarios', [ComentarioController::class, 'store'])->name('tickets.comentarios.store');

    Route::resource('usuarios', UsuarioController::class)->only(['index', 'update']);
});

require __DIR__.'/settings.php';
