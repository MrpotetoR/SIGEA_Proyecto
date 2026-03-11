<x-panel title="Alumnos" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6 items-end bg-white p-4 rounded-xl shadow-sm">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Buscar</label>
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                   placeholder="Nombre, apellido o matrícula..."
                   class="border rounded-lg px-3 py-2 text-sm w-56 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Carrera</label>
            <select name="carrera_id" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                <option value="">Todas</option>
                @foreach($carreras as $c)
                    <option value="{{ $c->id_carrera }}" @selected(request('carrera_id') == $c->id_carrera)>
                        {{ $c->nombre_carrera }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Estatus</label>
            <select name="estatus" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                <option value="">Todos</option>
                <option value="activo" @selected(request('estatus') === 'activo')>Activo</option>
                <option value="baja_temporal" @selected(request('estatus') === 'baja_temporal')>Baja temporal</option>
                <option value="baja_definitiva" @selected(request('estatus') === 'baja_definitiva')>Baja definitiva</option>
            </select>
        </div>
        <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Filtrar
        </button>
        <a href="{{ route('servicios.alumnos.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 py-2 px-2">Limpiar</a>

        <a href="{{ route('servicios.alumnos.create') }}"
           class="ml-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            + Nuevo alumno
        </a>
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3 text-left">Matrícula</th>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Carrera</th>
                    <th class="px-4 py-3 text-center">Cuatrimestre</th>
                    <th class="px-4 py-3 text-center">Estatus</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($alumnos as $alumno)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-gray-700">{{ $alumno->matricula }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $alumno->nombre_completo }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $alumno->carrera?->clave_carrera }}</td>
                        <td class="px-4 py-3 text-center">{{ $alumno->cuatrimestre_actual }}°</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $badge = match($alumno->estatus) {
                                    'activo' => 'bg-green-100 text-green-800',
                                    'baja_temporal' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-red-100 text-red-800',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">
                                {{ ucfirst(str_replace('_', ' ', $alumno->estatus)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('servicios.alumnos.show', $alumno) }}"
                                   class="text-indigo-600 hover:text-indigo-900 font-medium">Ver</a>
                                <a href="{{ route('servicios.alumnos.edit', $alumno) }}"
                                   class="text-yellow-600 hover:text-yellow-900 font-medium">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">No hay alumnos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($alumnos instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="px-4 py-3 border-t">{{ $alumnos->links() }}</div>
        @endif
    </div>
</x-panel>
