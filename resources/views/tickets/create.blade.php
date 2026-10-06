<x-layouts::app :title="__('Nuevo ticket')">
    <div class="max-w-2xl">
        <flux:heading size="xl">Nuevo ticket</flux:heading>
        <flux:subheading>Describe la incidencia o consulta que necesitas resolver.</flux:subheading>

        <form method="POST" action="{{ route('tickets.store') }}" class="mt-6 space-y-6">
            @csrf

            <flux:field>
                <flux:label>Título del ticket</flux:label>
                <flux:input type="text" name="titulo" value="{{ old('titulo') }}" placeholder="Ej: Falla en el inicio de sesión del sistema" required />
                <flux:error name="titulo" />
            </flux:field>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Categoría</flux:label>
                    <flux:select name="categoria_id" placeholder="Selecciona una categoría">
                        <flux:select.option value="">Sin categoría</flux:select.option>
                        @foreach ($categorias as $categoria)
                            <flux:select.option value="{{ $categoria->id }}" :selected="old('categoria_id') == $categoria->id">
                                {{ $categoria->nombre }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="categoria_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Prioridad</flux:label>
                    <flux:select name="prioridad">
                        <flux:select.option value="baja" :selected="old('prioridad') === 'baja'">Baja</flux:select.option>
                        <flux:select.option value="media" :selected="old('prioridad', 'media') === 'media'">Media</flux:select.option>
                        <flux:select.option value="alta" :selected="old('prioridad') === 'alta'">Alta</flux:select.option>
                        <flux:select.option value="urgente" :selected="old('prioridad') === 'urgente'">Urgente</flux:select.option>
                    </flux:select>
                    <flux:error name="prioridad" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Descripción detallada</flux:label>
                <flux:textarea name="descripcion" rows="6" placeholder="Describe los pasos para reproducir el problema o cualquier detalle relevante..." required>{{ old('descripcion') }}</flux:textarea>
                <flux:error name="descripcion" />
            </flux:field>

            <div class="flex items-center gap-3">
                <flux:button type="submit" variant="primary" icon="plus">Crear ticket</flux:button>
                <flux:button :href="route('tickets.index')" variant="ghost">Cancelar</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>