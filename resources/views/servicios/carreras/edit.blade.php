<x-panel title="Editar Carrera" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>
    <div class="max-w-lg">
        <a href="{{ route('servicios.carreras.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">← Volver</a>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <form method="POST" action="{{ route('servicios.carreras.update', $carrera) }}" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre *</label>
                    <input type="text" name="nombre_carrera" value="{{ old('nombre_carrera', $carrera->nombre_carrera) }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Clave</label>
                    <input type="text" value="{{ $carrera->clave_carrera }}" disabled
                           class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 dark:border-gray-600">
                    <p class="text-xs text-gray-400 dark:text-gray-400 mt-1">La clave no puede modificarse.</p>
                </div>
                <x-ajax-select
                    name="id_director"
                    :url="route('ajax.docentes')"
                    label="Director de carrera"
                    placeholder="Buscar docente..."
                    :value="$carrera->id_director"
                    :display="$carrera->director?->nombre_completo ?? ''"
                />
                <div class="flex gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="submit" class="bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">Guardar</button>
                    <a href="{{ route('servicios.carreras.index') }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
