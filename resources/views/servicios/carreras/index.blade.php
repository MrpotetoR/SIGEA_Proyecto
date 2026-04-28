<x-panel title="Carreras" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="flex items-center justify-between mb-6 gap-4">
        {{-- Filtro por tipo de periodo --}}
        <form method="GET" class="flex items-center gap-3">
            <label class="text-xs text-gray-500 dark:text-gray-400">Periodo:</label>
            <select name="tipo_periodo" onchange="this.form.submit()"
                class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Todos</option>
                <option value="cuatrimestre" @selected(request('tipo_periodo') === 'cuatrimestre')>Cuatrimestre</option>
                <option value="semestre" @selected(request('tipo_periodo') === 'semestre')>Semestre</option>
            </select>
        </form>

        <a href="{{ route('servicios.carreras.create') }}"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            + Nueva carrera
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 flex flex-col min-h-0"
        style="max-height: calc(100vh - 220px);">
        <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm bg-white dark:bg-gray-800">
                <thead
                    class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left">Clave</th>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Área</th>
                        <th class="px-4 py-3 text-center">Periodo</th>
                        <th class="px-4 py-3 text-center">Duración</th>
                        <th class="px-4 py-3 text-center">Alumnos</th>
                        <th class="px-4 py-3 text-left">Director</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($carreras as $c)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-4 py-3 font-mono font-bold text-blue-700 dark:text-blue-400">
                                            {{ $c->clave_carrera }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $c->nombre_carrera }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">
                                            {{ \App\Models\Carrera::AREAS_ACADEMICAS[$c->area_academica] ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold
                                                {{ $c->tipo_periodo === 'cuatrimestre'
                        ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
                        : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' }}">
                                                {{ ucfirst($c->tipo_periodo) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-xs text-gray-600 dark:text-gray-400">
                                            {{ $c->duracion_periodos }} per. <span
                                                class="text-gray-400">({{ $c->duracion_estimada }})</span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400 ">{{ $c->alumnos_count ?? 0 }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                            {{ $c->director?->nombre_completo ?? '—' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex justify-center gap-2">
                                                <a href="{{ route('servicios.carreras.edit', $c) }}"
                                                    class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 font-medium">Editar</a>
                                                <form method="POST" action="{{ route('servicios.carreras.destroy', $c) }}"
                                                    class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 dark:text-red-400 hover:text-red-900 font-medium"
                                                        onclick="return confirm('¿Eliminar carrera?')">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-400">No hay carreras registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-panel>

<script>
    (function () {
        const form = document.querySelector('form[method="GET"]');
        const selects = form.querySelectorAll('select');

        selects.forEach(s => s.addEventListener('change', () => form.submit()));
    })();
</script>