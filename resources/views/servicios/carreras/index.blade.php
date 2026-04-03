<x-panel title="Carreras" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="flex justify-end mb-6">
        <a href="{{ route('servicios.carreras.create') }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            + Nueva carrera
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 flex flex-col min-h-0" style="max-height: calc(100vh - 220px);">
        <div class="overflow-y-auto flex-1 custom-scrollbar">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3 text-left">Clave</th>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-center">Alumnos</th>
                    <th class="px-4 py-3 text-center">Materias</th>
                    <th class="px-4 py-3 text-left">Director</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($carreras as $c)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-mono font-bold text-blue-700 dark:text-blue-400">{{ $c->clave_carrera }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $c->nombre_carrera }}</td>
                        <td class="px-4 py-3 text-center">{{ $c->alumnos_count ?? 0 }}</td>
                        <td class="px-4 py-3 text-center">{{ $c->materias_count ?? 0 }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $c->director?->nombre_completo ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('servicios.carreras.edit', $c) }}"
                                   class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 font-medium">Editar</a>
                                <form method="POST" action="{{ route('servicios.carreras.destroy', $c) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 font-medium"
                                            onclick="return confirm('¿Eliminar carrera?')">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400 dark:text-gray-400">No hay carreras registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</x-panel>
