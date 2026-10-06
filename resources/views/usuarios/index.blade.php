<x-layouts::app :title="__('Gestión de Usuarios')">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Gestión de Usuarios</flux:heading>
            <flux:subheading>Administra las cuentas y los roles de acceso al sistema de tickets.</flux:subheading>
        </div>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle" class="mt-4">
            {{ session('success') }}
        </flux:callout>
    @endif

    @if (session('error'))
        <flux:callout variant="danger" icon="exclamation-circle" class="mt-4">
            {{ session('error') }}
        </flux:callout>
    @endif

    <!-- Buscador -->
    <form method="GET" action="{{ route('usuarios.index') }}" class="mt-5 flex gap-2 max-w-md">
        <flux:input
            name="q"
            value="{{ request('q') }}"
            placeholder="Buscar por nombre o correo..."
            icon="magnifying-glass"
            clearable
        />
        <flux:button type="submit">Buscar</flux:button>
        @if(request('q'))
            <flux:button :href="route('usuarios.index')" variant="ghost">Limpiar</flux:button>
        @endif
    </form>

    <div class="mt-5">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>Nombre</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column>Rol Actual</flux:table.column>
                <flux:table.column>Asignar Rol</flux:table.column>
                <flux:table.column>Fecha Registro</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($usuarios as $usuario)
                    <flux:table.row :key="$usuario->id">
                        <flux:table.cell>#{{ $usuario->id }}</flux:table.cell>
                        <flux:table.cell class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $usuario->name }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $usuario->email }}</flux:table.cell>
                        <flux:table.cell>
                            @php
                                $rol = $usuario->roles->first()?->name ?? 'sin rol';
                            @endphp
                            @if ($rol === 'admin')
                                <flux:badge color="purple">Administrador</flux:badge>
                            @elseif ($rol === 'supervisor')
                                <flux:badge color="amber">Supervisor</flux:badge>
                            @elseif ($rol === 'usuario')
                                <flux:badge color="blue">Usuario</flux:badge>
                            @else
                                <flux:badge color="zinc">{{ $rol }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <form method="POST" action="{{ route('usuarios.update', $usuario) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <flux:select name="role" size="sm" class="w-36">
                                    @foreach ($roles as $r)
                                        <flux:select.option value="{{ $r->name }}" :selected="$usuario->hasRole($r->name)">
                                            {{ ucfirst($r->name) }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:button type="submit" size="sm" variant="filled">Guardar</flux:button>
                            </form>
                        </flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-500">
                            {{ $usuario->created_at?->format('d/m/Y') }}
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="py-8 text-center text-zinc-500">
                            No se encontraron usuarios.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $usuarios->links() }}
        </div>
    </div>
</x-layouts::app>
