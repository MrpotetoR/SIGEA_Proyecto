<x-panel title="Detalle de Materia" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-2xl space-y-6">
        <a href="{{ route('servicios.materias.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline inline-block">← Volver</a>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $materia->nombre_materia }}</h2>
                    <p class="text-sm text-gray-400 dark:text-gray-400 mt-1">{{ $materia->carrera->nombre_carrera ?? '—' }}</p>
                </div>
                <a href="{{ route('servicios.materias.edit', $materia) }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Editar
                </a>
            </div>

            <dl class="grid grid-cols-2 gap-x-8 gap-y-4 text-sm">
                <div>
                    <dt class="text-gray-400 dark:text-gray-400 text-xs uppercase tracking-wide">Carrera</dt>
                    <dd class="mt-1 font-medium text-gray-800 dark:text-gray-200">{{ $materia->carrera->nombre_carrera ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 dark:text-gray-400 text-xs uppercase tracking-wide">Cuatrimestre</dt>
                    <dd class="mt-1 font-medium text-gray-800 dark:text-gray-200">{{ $materia->cuatrimestre }}°</dd>
                </div>
                <div>
                    <dt class="text-gray-400 dark:text-gray-400 text-xs uppercase tracking-wide">Horas por semana</dt>
                    <dd class="mt-1 font-medium text-gray-800 dark:text-gray-200">{{ $materia->horas_semana }} hrs</dd>
                </div>
                <div>
                    <dt class="text-gray-400 dark:text-gray-400 text-xs uppercase tracking-wide">ID Materia</dt>
                    <dd class="mt-1 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $materia->id_materia }}</dd>
                </div>
            </dl>

            <div class="flex gap-3 pt-6 mt-6 border-t dark:border-gray-700">
                <a href="{{ route('servicios.materias.edit', $materia) }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                    Editar materia
                </a>
                <form method="POST" action="{{ route('servicios.materias.destroy', $materia) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 text-sm font-medium px-5 py-2"
                            onclick="return confirm('¿Eliminar esta materia?')">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-panel>
