<x-panel title="Materias" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <form method="GET" class="flex gap-3 mb-6 items-end bg-white p-4 rounded-xl shadow-sm">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Carrera</label>
            <select name="carrera_id" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                <option value="">Todas</option>
                @foreach($carreras as $c)
                    <option value="{{ $c->id_carrera }}" @selected(request('carrera_id') == $c->id_carrera)>{{ $c->nombre_carrera }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Filtrar</button>
        <a href="{{ route('servicios.materias.create') }}"
           class="ml-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">+ Nueva materia</a>
    </form>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3 text-left">Materia</th>
                    <th class="px-4 py-3 text-left">Carrera</th>
                    <th class="px-4 py-3 text-center">Cuatrimestre</th>
                    <th class="px-4 py-3 text-center">Hrs/semana</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($materias as $m)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $m->nombre_materia }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $m->carrera?->clave_carrera }}</td>
                        <td class="px-4 py-3 text-center">{{ $m->cuatrimestre }}°</td>
                        <td class="px-4 py-3 text-center">{{ $m->horas_semana }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('servicios.materias.edit', $m) }}" class="text-yellow-600 hover:text-yellow-900 font-medium">Editar</a>
                                <form method="POST" action="{{ route('servicios.materias.destroy', $m) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium"
                                            onclick="return confirm('¿Eliminar materia?')">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">No hay materias registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($materias instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="px-4 py-3 border-t">{{ $materias->links() }}</div>
        @endif
    </div>
</x-panel>
