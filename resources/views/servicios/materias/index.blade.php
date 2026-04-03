<x-panel title="Materias" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <form method="GET" class="flex gap-3 mb-6 items-end bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm dark:shadow-gray-900/20 border border-transparent dark:border-gray-700">
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Carrera</label>
            <select name="carrera_id" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                <option value="">Todas</option>
                @foreach($carreras as $c)
                    <option value="{{ $c->id_carrera }}" @selected(request('carrera_id') == $c->id_carrera)>{{ $c->nombre_carrera }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-[#0606F0] hover:bg-[#04276B] dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Filtrar</button>
        <a href="{{ route('servicios.materias.create') }}"
           class="ml-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">+ Nueva materia</a>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 flex flex-col min-h-0" style="max-height: calc(100vh - 220px);">
        <div class="overflow-y-auto flex-1 custom-scrollbar">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3 text-left">Materia</th>
                    <th class="px-4 py-3 text-left">Carrera</th>
                    <th class="px-4 py-3 text-center">Cuatrimestre</th>
                    <th class="px-4 py-3 text-center">Hrs/semana</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($materias as $m)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $m->nombre_materia }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $m->carrera?->clave_carrera }}</td>
                        <td class="px-4 py-3 text-center">{{ $m->cuatrimestre }}°</td>
                        <td class="px-4 py-3 text-center">{{ $m->horas_semana }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('servicios.materias.edit', $m) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 font-medium">Editar</a>
                                <form method="POST" action="{{ route('servicios.materias.destroy', $m) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 font-medium"
                                            onclick="return confirm('¿Eliminar materia?')">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400 dark:text-gray-400">No hay materias registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($materias instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="px-4 py-3 border-t dark:border-gray-700 flex-shrink-0">{{ $materias->links() }}</div>
        @endif
    </div>
</x-panel>
