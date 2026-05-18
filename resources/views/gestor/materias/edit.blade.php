<x-panel title="Editar Materia" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>
    <div class="max-w-lg">
        <a href="{{ route('gestor.materias.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">← Volver</a>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <form method="POST" action="{{ route('gestor.materias.update', $materia) }}" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre de la materia *</label>
                    <input type="text" name="nombre_materia" value="{{ old('nombre_materia', $materia->nombre_materia) }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @php $maxPeriodos = $materia->carrera?->max_periodos ?? 10; $lblPeriodo = $materia->carrera?->label_periodo ?? 'Cuatrimestre'; @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $lblPeriodo }} *</label>
                        <select name="cuatrimestre" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                            @for($i = 1; $i <= $maxPeriodos; $i++)
                                <option value="{{ $i }}" @selected(old('cuatrimestre', $materia->cuatrimestre) == $i)>{{ $i }}°</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Horas/semana
                            <span class="text-gray-400 font-normal text-xs">(opcional)</span>
                        </label>
                        <input type="number" name="horas_semana" value="{{ old('horas_semana', $materia->horas_semana) }}" min="1" max="60"
                               placeholder="Ej: 4"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                        <p class="text-[10px] text-gray-400 mt-1">Déjalo vacío si aún no se define.</p>
                    </div>
                </div>
                <div class="flex gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="submit" class="bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">Guardar</button>
                    <a href="{{ route('gestor.materias.index') }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
