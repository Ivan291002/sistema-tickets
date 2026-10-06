<x-layouts::app :title="__('Tickets')">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Tickets</flux:heading>
            <flux:subheading>Gestiona y da seguimiento a las incidencias del sistema.</flux:subheading>
        </div>

        @can('crear tickets')
            <flux:button variant="primary" :href="route('tickets.create')" icon="plus">
                Nuevo ticket
            </flux:button>
        @endcan
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle" class="mt-4">
            {{ session('success') }}
        </flux:callout>
    @endif

    <!-- Barra de Filtros y Búsqueda -->
    <div class="mt-6 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900/50">
        <form method="GET" action="{{ route('tickets.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <div>
                <flux:input
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Buscar por título o #ID..."
                    icon="magnifying-glass"
                />
            </div>

            <div>
                <flux:select name="estado" placeholder="Todos los estados">
                    <flux:select.option value="">Todos los estados</flux:select.option>
                    <flux:select.option value="abierto" :selected="request('estado') === 'abierto'">Abiertos</flux:select.option>
                    <flux:select.option value="en_proceso" :selected="request('estado') === 'en_proceso'">En proceso</flux:select.option>
                    <flux:select.option value="cerrado" :selected="request('estado') === 'cerrado'">Cerrados</flux:select.option>
                </flux:select>
            </div>

            <div>
                <flux:select name="prioridad" placeholder="Todas las prioridades">
                    <flux:select.option value="">Todas las prioridades</flux:select.option>
                    <flux:select.option value="baja" :selected="request('prioridad') === 'baja'">Baja</flux:select.option>
                    <flux:select.option value="media" :selected="request('prioridad') === 'media'">Media</flux:select.option>
                    <flux:select.option value="alta" :selected="request('prioridad') === 'alta'">Alta</flux:select.option>
                    <flux:select.option value="urgente" :selected="request('prioridad') === 'urgente'">Urgente</flux:select.option>
                </flux:select>
            </div>

            <div>
                <flux:select name="categoria_id" placeholder="Todas las categorías">
                    <flux:select.option value="">Todas las categorías</flux:select.option>
                    @foreach ($categorias as $cat)
                        <flux:select.option value="{{ $cat->id }}" :selected="request('categoria_id') == $cat->id">
                            {{ $cat->nombre }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="flex items-center gap-2">
                <flux:button type="submit" variant="filled" class="flex-1">Filtrar</flux:button>
                @if (request()->hasAny(['q', 'estado', 'prioridad', 'categoria_id']))
                    <flux:button :href="route('tickets.index')" variant="ghost">Limpiar</flux:button>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabla de Tickets -->
    <div class="mt-6">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>Título & Categoría</flux:table.column>
                <flux:table.column>Autor</flux:table.column>
                <flux:table.column>Asignado a</flux:table.column>
                <flux:table.column>Prioridad</flux:table.column>
                <flux:table.column>Estado</flux:table.column>
                <flux:table.column>Fecha</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($tickets as $ticket)
                    <flux:table.row :key="$ticket->id">
                        <flux:table.cell class="font-mono text-xs text-zinc-500">#{{ $ticket->id }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-zinc-900 hover:underline dark:text-zinc-100">
                                    {{ $ticket->titulo }}
                                </a>
                                @if ($ticket->categoria)
                                    <span class="mt-0.5 text-xs text-zinc-500">
                                        📁 {{ $ticket->categoria->nombre }}
                                    </span>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>{{ $ticket->user->name }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($ticket->asignado)
                                <span class="inline-flex items-center gap-1.5 text-xs text-zinc-700 dark:text-zinc-300">
                                    <span class="inline-block size-2 rounded-full bg-emerald-500"></span>
                                    {{ $ticket->asignado->name }}
                                </span>
                            @else
                                <span class="text-xs italic text-zinc-400">Sin asignar</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($ticket->prioridad === 'urgente')
                                <flux:badge color="red">Urgente</flux:badge>
                            @elseif ($ticket->prioridad === 'alta')
                                <flux:badge color="amber">Alta</flux:badge>
                            @elseif ($ticket->prioridad === 'media')
                                <flux:badge color="blue">Media</flux:badge>
                            @else
                                <flux:badge color="zinc">Baja</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($ticket->estado === 'abierto')
                                <flux:badge color="blue">Abierto</flux:badge>
                            @elseif ($ticket->estado === 'en_proceso')
                                <flux:badge color="amber">En proceso</flux:badge>
                            @else
                                <flux:badge color="green">Cerrado</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-500">
                            {{ $ticket->created_at?->format('d/m/Y') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1.5">
                                <flux:button :href="route('tickets.show', $ticket)" size="sm">Ver</flux:button>

                                @can('editar tickets')
                                    <flux:button :href="route('tickets.edit', $ticket)" size="sm">Editar</flux:button>
                                @endcan

                                @if ($ticket->estado !== 'cerrado' && Auth::user()->can('cerrar tickets'))
                                    <form method="POST" action="{{ route('tickets.cerrar', $ticket) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <flux:button type="submit" variant="danger" size="sm">Cerrar</flux:button>
                                    </form>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="py-12 text-center text-zinc-500">
                            No se encontraron tickets con los criterios seleccionados.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</x-layouts::app>