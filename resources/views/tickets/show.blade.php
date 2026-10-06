<x-layouts::app :title="__('Ticket #'.$ticket->id.' - '.$ticket->titulo)">
    <div class="space-y-6">
        <!-- Encabezado -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-sm text-zinc-500">#{{ $ticket->id }}</span>
                    @if ($ticket->estado === 'abierto')
                        <flux:badge color="blue">Abierto</flux:badge>
                    @elseif ($ticket->estado === 'en_proceso')
                        <flux:badge color="amber">En proceso</flux:badge>
                    @else
                        <flux:badge color="green">Cerrado</flux:badge>
                    @endif

                    @if ($ticket->prioridad === 'urgente')
                        <flux:badge color="red">Urgente</flux:badge>
                    @elseif ($ticket->prioridad === 'alta')
                        <flux:badge color="amber">Prioridad Alta</flux:badge>
                    @elseif ($ticket->prioridad === 'media')
                        <flux:badge color="blue">Prioridad Media</flux:badge>
                    @else
                        <flux:badge color="zinc">Prioridad Baja</flux:badge>
                    @endif
                </div>
                <flux:heading size="xl" class="mt-2">{{ $ticket->titulo }}</flux:heading>
                <flux:subheading>Creado por {{ $ticket->user->name }} el {{ $ticket->created_at?->format('d/m/Y H:i') }}</flux:subheading>
            </div>

            <div class="flex items-center gap-2">
                <flux:button :href="route('tickets.index')" variant="ghost" icon="arrow-left">Volver</flux:button>

                @can('editar tickets')
                    <flux:button :href="route('tickets.edit', $ticket)" icon="pencil-square">Editar</flux:button>
                @endcan
            </div>
        </div>

        @if (session('success'))
            <flux:callout variant="success" icon="check-circle">
                {{ session('success') }}
            </flux:callout>
        @endif

        <!-- Panel de Metadatos y Gestión -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Categoría</div>
                <div class="mt-2 text-sm font-medium text-zinc-800 dark:text-zinc-200">
                    {{ $ticket->categoria?->nombre ?? 'Sin categoría' }}
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Responsable Asignado</div>
                <div class="mt-2 text-sm font-medium text-zinc-800 dark:text-zinc-200">
                    @if ($ticket->asignado)
                        👤 {{ $ticket->asignado->name }}
                    @else
                        <span class="italic text-zinc-400">No asignado</span>
                    @endif
                </div>

                @can('editar tickets')
                    <form method="POST" action="{{ route('tickets.asignar', $ticket) }}" class="mt-3 flex gap-2">
                        @csrf
                        @method('PATCH')
                        <flux:select name="asignado_a" size="sm" class="flex-1">
                            <flux:select.option value="">Sin asignar</flux:select.option>
                            @foreach ($supervisores as $sup)
                                <flux:select.option value="{{ $sup->id }}" :selected="$ticket->asignado_a == $sup->id">
                                    {{ $sup->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:button type="submit" size="sm">Asignar</flux:button>
                    </form>
                @endcan
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Cambiar Estado</div>
                <div class="mt-2 text-sm font-medium text-zinc-800 dark:text-zinc-200">
                    Estado actual: <span class="capitalize">{{ str_replace('_', ' ', $ticket->estado) }}</span>
                </div>

                @can('cerrar tickets')
                    <form method="POST" action="{{ route('tickets.estado', $ticket) }}" class="mt-3 flex gap-2">
                        @csrf
                        @method('PATCH')
                        <flux:select name="estado" size="sm" class="flex-1">
                            <flux:select.option value="abierto" :selected="$ticket->estado === 'abierto'">Abierto</flux:select.option>
                            <flux:select.option value="en_proceso" :selected="$ticket->estado === 'en_proceso'">En proceso</flux:select.option>
                            <flux:select.option value="cerrado" :selected="$ticket->estado === 'cerrado'">Cerrado</flux:select.option>
                        </flux:select>
                        <flux:button type="submit" size="sm">Actualizar</flux:button>
                    </form>
                @endcan
            </div>
        </div>

        <!-- Descripción -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-zinc-400">Descripción del problema</h3>
            <p class="mt-4 whitespace-pre-line text-zinc-800 leading-relaxed dark:text-zinc-200">
                {{ $ticket->descripcion }}
            </p>
        </div>

        <!-- Sección de Respuestas / Comentarios -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg">Respuestas y Seguimiento ({{ $ticket->comentarios->count() }})</flux:heading>
            <flux:subheading>Historial de comunicación sobre este ticket.</flux:subheading>

            <div class="mt-6 space-y-4">
                @forelse ($ticket->comentarios as $comentario)
                    <div class="rounded-lg border border-zinc-100 bg-zinc-50/60 p-4 dark:border-zinc-800 dark:bg-zinc-800/40">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $comentario->user->name }}
                                </span>
                                @php
                                    $rolComentario = $comentario->user->roles->first()?->name;
                                @endphp
                                @if ($rolComentario === 'admin')
                                    <flux:badge size="sm" color="purple">Admin</flux:badge>
                                @elseif ($rolComentario === 'supervisor')
                                    <flux:badge size="sm" color="amber">Soporte</flux:badge>
                                @else
                                    <flux:badge size="sm" color="blue">Usuario</flux:badge>
                                @endif
                            </div>
                            <span class="text-xs text-zinc-400">
                                {{ $comentario->created_at?->diffForHumans() }}
                            </span>
                        </div>
                        <div class="mt-2 text-sm text-zinc-700 whitespace-pre-line leading-relaxed dark:text-zinc-300">
                            {{ $comentario->contenido }}
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-sm text-zinc-400">
                        Aún no hay respuestas en este ticket. Sé el primero en responder.
                    </div>
                @endforelse
            </div>

            <!-- Formulario de nuevo comentario -->
            <form method="POST" action="{{ route('tickets.comentarios.store', $ticket) }}" class="mt-6 border-t border-zinc-200 pt-6 dark:border-zinc-700">
                @csrf
                <flux:field>
                    <flux:label>Agregar una respuesta o nota de seguimiento</flux:label>
                    <flux:textarea
                        name="contenido"
                        rows="4"
                        placeholder="Escribe tu mensaje, actualización o solución aquí..."
                        required
                    >{{ old('contenido') }}</flux:textarea>
                    <flux:error name="contenido" />
                </flux:field>

                <div class="mt-3 flex justify-end">
                    <flux:button type="submit" variant="primary" icon="paper-airplane">
                        Enviar respuesta
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>