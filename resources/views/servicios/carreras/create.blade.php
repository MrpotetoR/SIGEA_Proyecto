<x-panel title="Nueva Carrera" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>
    <div class="max-w-lg">
        <a href="{{ route('servicios.carreras.index') }}" class="text-sm text-indigo-600 hover:underline mb-6 inline-block">← Volver</a>
        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ route('servicios.carreras.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la carrera *</label>
                    <input type="text" name="nombre_carrera" value="{{ old('nombre_carrera') }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none @error('nombre_carrera') border-red-400 @enderror">
                    @error('nombre_carrera')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Clave *</label>
                    <input type="text" name="clave_carrera" value="{{ old('clave_carrera') }}" required
                           placeholder="Ej: DSM, GE, ADM"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none @error('clave_carrera') border-red-400 @enderror">
                    @error('clave_carrera')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <x-ajax-select
                    name="id_director"
                    :url="route('ajax.docentes')"
                    label="Director de carrera"
                    placeholder="Buscar docente..."
                />
                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="bg-indigo-700 hover:bg-indigo-800 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">Crear carrera</button>
                    <a href="{{ route('servicios.carreras.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
