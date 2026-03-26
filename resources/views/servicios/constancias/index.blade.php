<x-panel title="Constancias" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Generar constancia --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-base font-semibold text-gray-700 mb-4">Generar constancia</h3>
            <form method="POST" action="{{ route('servicios.constancias.store') }}" class="space-y-4">
                @csrf
                <x-ajax-select
                    name="id_alumno"
                    :url="route('ajax.alumnos')"
                    label="Alumno *"
                    placeholder="Nombre o matrícula..."
                    :required="true"
                />
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Tipo de constancia *</label>
                    <select name="tipo" required
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        <option value="estudio">De estudio</option>
                        <option value="calificaciones">De calificaciones</option>
                        <option value="comportamiento">De comportamiento</option>
                        <option value="servicio_social">Servicio social</option>
                        <option value="cultural">Actividades culturales</option>
                    </select>
                </div>
                <button type="submit"
                        class="w-full bg-indigo-700 hover:bg-indigo-800 text-white py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    Generar PDF
                </button>
            </form>
        </div>

        {{-- Historial --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold text-gray-700">Constancias emitidas</h3>
                <form method="GET" class="flex gap-2">
                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                           placeholder="Matrícula..."
                           class="border rounded-lg px-2 py-1.5 text-sm w-32 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg text-sm text-gray-700">Buscar</button>
                </form>
            </div>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Alumno</th>
                        <th class="px-4 py-3 text-center">Tipo</th>
                        <th class="px-4 py-3 text-center">Fecha</th>
                        <th class="px-4 py-3 text-center">Emitida por</th>
                        <th class="px-4 py-3 text-center">PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($constancias as $c)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ $c->alumno?->nombre_completo }}</p>
                                <p class="text-xs text-gray-400">{{ $c->alumno?->matricula }}</p>
                            </td>
                            <td class="px-4 py-3 text-center capitalize">{{ str_replace('_', ' ', $c->tipo) }}</td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $c->fecha_emision?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $c->generadaPor?->name }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('servicios.constancias.pdf', $c) }}"
                                   class="text-indigo-600 hover:underline text-xs font-medium">Descargar</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Sin constancias emitidas.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($constancias instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="px-4 py-3 border-t">{{ $constancias->links() }}</div>
            @endif
        </div>
    </div>
</x-panel>
