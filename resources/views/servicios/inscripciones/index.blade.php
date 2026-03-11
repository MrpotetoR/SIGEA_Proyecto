<x-panel title="Inscripciones" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Formulario nueva inscripción --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-base font-semibold text-gray-700 mb-4">Nueva inscripción</h3>
            <form method="POST" action="{{ route('servicios.inscripciones.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Alumno *</label>
                    <select name="id_alumno" required
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        <option value="">Seleccionar alumno...</option>
                        @foreach($alumnos as $a)
                            <option value="{{ $a->id_alumno }}">{{ $a->apellidos }}, {{ $a->nombre }} — {{ $a->matricula }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Grupo *</label>
                    <select name="id_grupo" required
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        <option value="">Seleccionar grupo...</option>
                        @foreach($grupos as $g)
                            <option value="{{ $g->id_grupo }}">{{ $g->clave_grupo }} — {{ $g->cicloEscolar?->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="w-full bg-indigo-700 hover:bg-indigo-800 text-white py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    Inscribir alumno
                </button>
            </form>
        </div>

        {{-- Lista de inscripciones --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold text-gray-700">Inscripciones recientes</h3>
                <form method="GET" class="flex gap-2">
                    <select name="grupo_id" class="border rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        <option value="">Todos los grupos</option>
                        @foreach($grupos as $g)
                            <option value="{{ $g->id_grupo }}" @selected(request('grupo_id') == $g->id_grupo)>{{ $g->clave_grupo }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg text-sm text-gray-700">Filtrar</button>
                </form>
            </div>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Alumno</th>
                        <th class="px-4 py-3 text-left">Grupo</th>
                        <th class="px-4 py-3 text-center">Ciclo</th>
                        <th class="px-4 py-3 text-center">Fecha</th>
                        <th class="px-4 py-3 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($inscripciones as $i)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $i->alumno?->nombre_completo }}</p>
                                <p class="text-xs text-gray-400">{{ $i->alumno?->matricula }}</p>
                            </td>
                            <td class="px-4 py-3 font-mono text-gray-700">{{ $i->grupo?->clave_grupo }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $i->grupo?->cicloEscolar?->nombre }}</td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $i->fecha_inscripcion?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <form method="POST" action="{{ route('servicios.inscripciones.destroy', $i) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium"
                                            onclick="return confirm('¿Eliminar inscripción?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No hay inscripciones.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($inscripciones instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="px-4 py-3 border-t">{{ $inscripciones->links() }}</div>
            @endif
        </div>
    </div>
</x-panel>
