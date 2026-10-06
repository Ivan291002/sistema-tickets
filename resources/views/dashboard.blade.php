<x-layouts::app :title="__('Dashboard')">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Panel de Control</flux:heading>
            <flux:subheading>Resumen general del sistema de tickets y actividad reciente.</flux:subheading>
        </div>

        <div class="flex items-center gap-2">
            @can('crear tickets')
                <flux:button variant="primary" :href="route('tickets.create')" icon="plus">
                    Nuevo ticket
                </flux:button>
            @endcan
            <flux:button :href="route('tickets.index')" icon="ticket">
                Ver todos
            </flux:button>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total de Tickets</div>
                <div class="rounded-lg bg-zinc-100 p-2 dark:bg-zinc-800">
                    <span class="text-zinc-600 dark:text-zinc-300">📊</span>
                </div>
            </div>
            <div class="mt-3 text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $total ?? ($abiertos + $enProceso + $cerrados) }}</div>
            <p class="mt-1 text-xs text-zinc-500">Tickets registrados en el sistema</p>
        </div>

        <div class="rounded-xl border border-blue-100 bg-blue-50/40 p-5 shadow-xs dark:border-blue-900/40 dark:bg-blue-950/20">
            <div class="flex items-center justify-between">
                <div class="text-sm font-medium text-blue-700 dark:text-blue-400">Abiertos</div>
                <div class="rounded-lg bg-blue-100 p-2 dark:bg-blue-900/50">
                    <span class="text-blue-600">🔔</span>
                </div>
            </div>
            <div class="mt-3 text-3xl font-bold text-blue-900 dark:text-blue-300">{{ $abiertos }}</div>
            <p class="mt-1 text-xs text-blue-600/80 dark:text-blue-400/80">Pendientes de atención inicial</p>
        </div>

        <div class="rounded-xl border border-amber-100 bg-amber-50/40 p-5 shadow-xs dark:border-amber-900/40 dark:bg-amber-950/20">
            <div class="flex items-center justify-between">
                <div class="text-sm font-medium text-amber-700 dark:text-amber-400">En Proceso</div>
                <div class="rounded-lg bg-amber-100 p-2 dark:bg-amber-900/50">
                    <span class="text-amber-600">⏳</span>
                </div>
            </div>
            <div class="mt-3 text-3xl font-bold text-amber-900 dark:text-amber-300">{{ $enProceso }}</div>
            <p class="mt-1 text-xs text-amber-600/80 dark:text-amber-400/80">Con seguimiento activo</p>
        </div>

        <div class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-5 shadow-xs dark:border-emerald-900/40 dark:bg-emerald-950/20">
            <div class="flex items-center justify-between">
                <div class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Cerrados / Resueltos</div>
                <div class="rounded-lg bg-emerald-100 p-2 dark:bg-emerald-900/50">
                    <span class="text-emerald-600">✅</span>
                </div>
            </div>
            <div class="mt-3 text-3xl font-bold text-emerald-900 dark:text-emerald-300">{{ $cerrados }}</div>
            <p class="mt-1 text-xs text-emerald-600/80 dark:text-emerald-400/80">Casos solucionados con éxito</p>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="mt-8 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg">Tickets Recientes</flux:heading>
                <flux:subheading>Últimas incidencias registradas en la plataforma.</flux:subheading>
            </div>
            <flux:button :href="route('tickets.index')" size="sm" variant="ghost">Ver todos los tickets →</flux:button>
        </div>

        <div class="mt-4">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>#ID</flux:table.column>
                    <flux:table.column>Título</flux:table.column>
                    <flux:table.column>Categoría</flux:table.column>
                    <flux:table.column>Prioridad</flux:table.column>
                    <flux:table.column>Estado</flux:table.column>
                    <flux:table.column>Fecha</flux:table.column>
                    <flux:table.column>Acción</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($recientes ?? [] as $t)
                        <flux:table.row :key="$t->id">
                            <flux:table.cell class="font-mono text-xs text-zinc-500">#{{ $t->id }}</flux:table.cell>
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $t->titulo }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $t->categoria?->nombre ?? 'General' }}
                            </flux:table.cell>
                            <flux:table.cell>
                                @if ($t->prioridad === 'urgente')
                                    <flux:badge size="sm" color="red">Urgente</flux:badge>
                                @elseif ($t->prioridad === 'alta')
                                    <flux:badge size="sm" color="amber">Alta</flux:badge>
                                @elseif ($t->prioridad === 'media')
                                    <flux:badge size="sm" color="blue">Media</flux:badge>
                                @else
                                    <flux:badge size="sm" color="zinc">Baja</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @if ($t->estado === 'abierto')
                                    <flux:badge size="sm" color="blue">Abierto</flux:badge>
                                @elseif ($t->estado === 'en_proceso')
                                    <flux:badge size="sm" color="amber">En proceso</flux:badge>
                                @else
                                    <flux:badge size="sm" color="green">Cerrado</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="text-xs text-zinc-500">
                                {{ $t->created_at?->format('d/m/Y') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button :href="route('tickets.show', $t)" size="sm">Ver</flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="py-8 text-center text-zinc-500">
                                No hay tickets registrados aún.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</x-layouts::app>