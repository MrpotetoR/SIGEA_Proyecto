<x-panel title="Alumnos" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6 items-end bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm dark:shadow-gray-900/20 border border-transparent dark:border-gray-700">
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                   placeholder="Nombre, apellido o matrícula..."
                   class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm w-56 focus:ring-2 focus:ring-indigo-400 focus:outline-none dark:placeholder-gray-400">
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Carrera</label>
            <select name="carrera_id" class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                <option value="">Todas</option>
                @foreach($carreras as $c)
                    <option value="{{ $c->id_carrera }}" @selected(request('carrera_id') == $c->id_carrera)>
                        {{ $c->nombre_carrera }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Estatus</label>
            <select name="estatus" class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                <option value="">Todos</option>
                <option value="activo" @selected(request('estatus') === 'activo')>Activo</option>
                <option value="baja_temporal" @selected(request('estatus') === 'baja_temporal')>Baja temporal</option>
                <option value="baja_definitiva" @selected(request('estatus') === 'baja_definitiva')>Baja definitiva</option>
            </select>
        </div>
        <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Filtrar
        </button>
        <a href="{{ route('servicios.alumnos.index') }}"
           class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 py-2 px-2">Limpiar</a>

        <a href="{{ route('servicios.alumnos.create') }}"
           class="ml-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            + Nuevo alumno
        </a>
    </form>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 overflow-hidden border border-transparent dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 text-left">Matrícula</th>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Carrera</th>
                    <th class="px-4 py-3 text-center">Cuatrimestre</th>
                    <th class="px-4 py-3 text-center">Estatus</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($alumnos as $alumno)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-mono text-gray-700 dark:text-gray-300">{{ $alumno->matricula }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $alumno->nombre_completo }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $alumno->carrera?->clave_carrera }}</td>
                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $alumno->cuatrimestre_actual }}°</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $badge = match($alumno->estatus) {
                                    'activo' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                    'baja_temporal' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                    default => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">
                                {{ ucfirst(str_replace('_', ' ', $alumno->estatus)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('servicios.alumnos.show', $alumno) }}"
                                   class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-medium">Ver</a>
                                <a href="{{ route('servicios.alumnos.edit', $alumno) }}"
                                   class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 font-medium">Editar</a>
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
            <div class="px-4 py-3 border-t dark:border-gray-700">{{ $alumnos->links() }}</div>
        @endif
    </div>
</x-panel>
