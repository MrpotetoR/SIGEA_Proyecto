<x-panel title="Planes de Bachillerato" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Planes de Bachillerato</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Administra los planes de estudios del nivel bachillerato.</p>
        </div>
        <a href="{{ route('gestor.planes-bachillerato.create') }}"
           class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Plan
        </a>
    </div>

    @if(session('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-[11px] uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-left">Clave</th>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-center">Modalidad</th>
                    <th class="px-4 py-3 text-center">Duracion</th>
                    <th class="px-4 py-3 text-center">Materias</th>
                    <th class="px-4 py-3 text-center">Grupos</th>
                    <th class="px-4 py-3 text-center">Alumnos</th>
                    <th class="px-4 py-3 text-center">Estatus</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($planes as $p)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-4 py-3 font-mono text-gray-700 dark:text-gray-300">{{ $p->clave_plan }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $p->nombre_plan }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($p->tipo_periodo === 'cuatrimestre')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">No Escolarizado</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">Escolarizado</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">
                            <span class="text-xs">{{ $p->duracion_texto }}</span>
                            <span class="block text-[10px] text-gray-400">{{ $p->num_semestres }} {{ $p->label_periodo }}s</span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $p->materias_count }}</td>
                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $p->grupos_count }}</td>
                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $p->alumnos_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($p->vigente)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Vigente</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('gestor.planes-bachillerato.edit', $p) }}"
                               class="inline-flex items-center text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 text-xs font-semibold mr-3">
                                Editar
                            </a>
                            <form action="{{ route('gestor.planes-bachillerato.destroy', $p) }}" method="POST" class="inline"
                                  data-udea-confirm
                                  data-confirm-title="Eliminar plan de estudios"
                                  data-confirm-message="¿Eliminar el plan <strong>&quot;{{ $p->clave_plan }}&quot;</strong>?"
                                  data-confirm-detail="Esta acción no se puede deshacer."
                                  data-confirm-variant="danger"
                                  data-confirm-icon="trash"
                                  data-confirm-button="Eliminar"
                                  data-confirm-cancel="Cancelar">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs font-semibold">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center text-gray-400">
                            No hay planes de bachillerato. <a href="{{ route('gestor.planes-bachillerato.create') }}" class="text-amber-600 hover:underline">Crear el primero →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $planes->links() }}</div>
</x-panel>
