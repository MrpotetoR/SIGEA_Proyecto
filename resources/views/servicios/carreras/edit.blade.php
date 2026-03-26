<x-panel title="Editar Carrera" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>
    <div class="max-w-lg">
        <a href="{{ route('servicios.carreras.index') }}" class="text-sm text-indigo-600 hover:underline mb-6 inline-block">← Volver</a>
        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ route('servicios.carreras.update', $carrera) }}" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="nombre_carrera" value="{{ old('nombre_carrera', $carrera->nombre_carrera) }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Clave</label>
                    <input type="text" value="{{ $carrera->clave_carrera }}" disabled
                           class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500">
                    <p class="text-xs text-gray-400 mt-1">La clave no puede modificarse.</p>
                </div>
                <x-ajax-select
                    name="id_director"
                    :url="route('ajax.docentes')"
                    label="Director de carrera"
                    placeholder="Buscar docente..."
                    :value="$carrera->id_director"
                    :display="$carrera->director?->nombre_completo ?? ''"
                />
                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="bg-indigo-700 hover:bg-indigo-800 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">Guardar</button>
                    <a href="{{ route('servicios.carreras.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
