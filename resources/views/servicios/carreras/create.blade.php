<x-panel title="Nueva Carrera" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>
    <div class="max-w-lg">
        <a href="{{ route('servicios.carreras.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline mb-6 inline-block">← Volver</a>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <form method="POST" action="{{ route('servicios.carreras.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre de la carrera *</label>
                    <input type="text" name="nombre_carrera" value="{{ old('nombre_carrera') }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 @error('nombre_carrera') border-red-400 @enderror">
                    @error('nombre_carrera')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Clave *</label>
                    <input type="text" name="clave_carrera" value="{{ old('clave_carrera') }}" required
                           placeholder="Ej: DSM, GE, ADM"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 @error('clave_carrera') border-red-400 @enderror">
                    @error('clave_carrera')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <x-ajax-select
                    name="id_director"
                    :url="route('ajax.docentes')"
                    label="Director de carrera"
                    placeholder="Buscar docente..."
                />
                <div class="flex gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="submit" class="bg-indigo-700 hover:bg-indigo-800 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">Crear carrera</button>
                    <a href="{{ route('servicios.carreras.index') }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
