<x-panel title="Nueva Materia" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>
    <div class="max-w-lg">
        <a href="{{ route('servicios.materias.index') }}" class="text-sm text-indigo-600 hover:underline mb-6 inline-block">← Volver</a>
        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ route('servicios.materias.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la materia *</label>
                    <input type="text" name="nombre_materia" value="{{ old('nombre_materia') }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none @error('nombre_materia') border-red-400 @enderror">
                    @error('nombre_materia')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Carrera *</label>
                    <select name="id_carrera" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        <option value="">Seleccionar...</option>
                        @foreach($carreras as $c)
                            <option value="{{ $c->id_carrera }}" @selected(old('id_carrera') == $c->id_carrera)>{{ $c->nombre_carrera }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cuatrimestre *</label>
                        <select name="cuatrimestre" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" @selected(old('cuatrimestre') == $i)>{{ $i }}°</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Horas/semana *</label>
                        <input type="number" name="horas_semana" value="{{ old('horas_semana', 1) }}" required min="1"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                    </div>
                </div>
                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="bg-indigo-700 hover:bg-indigo-800 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">Crear materia</button>
                    <a href="{{ route('servicios.materias.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
