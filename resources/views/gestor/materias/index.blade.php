<x-panel title="Materias" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <form method="GET"
        class="flex flex-wrap gap-3 mb-6 items-end bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm dark:shadow-gray-900/20 border border-transparent dark:border-gray-700">
        <div class="flex-1 min-w-[180px]">
            @if($esBachi ?? false)
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Plan</label>
                <select name="plan_id"
                    class="border rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-amber-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    <option value="">Todos</option>
                    @foreach($planesBachi as $p)
                        <option value="{{ $p->id_plan_bachillerato }}" @selected(request('plan_id') == $p->id_plan_bachillerato)>
                            {{ $p->nombre_plan }}</option>
                    @endforeach
                </select>
            @else
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Carrera</label>
                <select name="carrera_id"
                    class="border rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    <option value="">Todas</option>
                    @foreach($carreras as $c)
                        <option value="{{ $c->id_carrera }}" @selected(request('carrera_id') == $c->id_carrera)>
                            {{ $c->nombre_carrera }}</option>
                    @endforeach
                </select>
            @endif
        </div>
        <button type="submit"
            class="bg-[#0606F0] hover:bg-[#04276B] dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">Filtrar</button>
        <a href="{{ route('gestor.materias.create') }}"
            class="sm:ml-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap w-full sm:w-auto text-center">+
            Nueva materia</a>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 flex flex-col min-h-0"
        style="max-height: calc(100vh - 220px);">
        <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm bg-white dark:bg-gray-800">
                <thead
                    class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left">Materia</th>
                        <th class="px-4 py-3 text-left">{{ ($esBachi ?? false) ? 'Plan' : 'Carrera' }}</th>
                        <th class="px-4 py-3 text-center">{{ ($esBachi ?? false) ? 'Semestre' : 'Cuatrimestre' }}</th>
                        <th class="px-4 py-3 text-center">Hrs/semana</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($materias as $m)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $m->nombre_materia }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                {{ $m->nivel_educativo === 'bachillerato'
                                    ? ($m->planBachillerato?->clave_plan ?? '—')
                                    : ($m->carrera?->clave_carrera ?? '—') }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $m->cuatrimestre }}°</td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">
                                {{ $m->horas_semana ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('gestor.materias.edit', $m) }}"
                                        class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 font-medium">Editar</a>
                                    <form method="POST" action="{{ route('gestor.materias.destroy', $m) }}"
                                        class="inline"
                                        data-udea-confirm
                                        data-confirm-title="Eliminar materia"
                                        data-confirm-message="¿Eliminar la materia <strong>&quot;{{ $m->nombre_materia ?? $m->nombre ?? 'seleccionada' }}&quot;</strong>?"
                                        data-confirm-detail="Esta acción no se puede deshacer."
                                        data-confirm-variant="danger"
                                        data-confirm-icon="trash"
                                        data-confirm-button="Eliminar"
                                        data-confirm-cancel="Cancelar">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 font-medium">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-400 dark:text-gray-400">No hay materias
                                registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($materias instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="px-4 py-3 border-t dark:border-gray-700 flex-shrink-0">{{ $materias->links() }}</div>
        @endif
    </div>
</x-panel>

<script>
    (function () {
        const form = document.querySelector('form[method="GET"]');
        const selects = form.querySelectorAll('select');

        selects.forEach(s => s.addEventListener('change', () => form.submit()));
    })();
</script>