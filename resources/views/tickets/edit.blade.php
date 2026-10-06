<x-layouts::app :title="__('Editar ticket #'.$ticket->id)">
    <div class="max-w-2xl">
        <flux:heading size="xl">Editar ticket #{{ $ticket->id }}</flux:heading>
        <flux:subheading>Actualiza el estado, prioridad o asignación del ticket.</flux:subheading>

        <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            <flux:field>
                <flux:label>Título</flux:label>
                <flux:input type="text" name="titulo" value="{{ old('titulo', $ticket->titulo) }}" required />
                <flux:error name="titulo" />
            </flux:field>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Categoría</flux:label>
                    <flux:select name="categoria_id">
                        <flux:select.option value="">Sin categoría</flux:select.option>
                        @foreach ($categorias as $categoria)
                            <flux:select.option value="{{ $categoria->id }}" :selected="old('categoria_id', $ticket->categoria_id) == $categoria->id">
                                {{ $categoria->nombre }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="categoria_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Prioridad</flux:label>
                    <flux:select name="prioridad">
                        <flux:select.option value="baja" :selected="old('prioridad', $ticket->prioridad) === 'baja'">Baja</flux:select.option>
                        <flux:select.option value="media" :selected="old('prioridad', $ticket->prioridad) === 'media'">Media</flux:select.option>
                        <flux:select.option value="alta" :selected="old('prioridad', $ticket->prioridad) === 'alta'">Alta</flux:select.option>
                        <flux:select.option value="urgente" :selected="old('prioridad', $ticket->prioridad) === 'urgente'">Urgente</flux:select.option>
                    </flux:select>
                    <flux:error name="prioridad" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Estado</flux:label>
                    <flux:select name="estado">
                        <flux:select.option value="abierto" :selected="old('estado', $ticket->estado) === 'abierto'">Abierto</flux:select.option>
                        <flux:select.option value="en_proceso" :selected="old('estado', $ticket->estado) === 'en_proceso'">En proceso</flux:select.option>
                        <flux:select.option value="cerrado" :selected="old('estado', $ticket->estado) === 'cerrado'">Cerrado</flux:select.option>
                    </flux:select>
                    <flux:error name="estado" />
                </flux:field>

                <flux:field>
                    <flux:label>Responsable asignado</flux:label>
                    <flux:select name="asignado_a">
                        <flux:select.option value="">Sin asignar</flux:select.option>
                        @foreach ($supervisores as $sup)
                            <flux:select.option value="{{ $sup->id }}" :selected="old('asignado_a', $ticket->asignado_a) == $sup->id">
                                {{ $sup->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="asignado_a" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Descripción</flux:label>
                <flux:textarea name="descripcion" rows="6" required>{{ old('descripcion', $ticket->descripcion) }}</flux:textarea>
                <flux:error name="descripcion" />
            </flux:field>

            <div class="flex items-center gap-3">
                <flux:button type="submit" variant="primary">Guardar cambios</flux:button>
                <flux:button :href="route('tickets.show', $ticket)" variant="ghost">Cancelar</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>