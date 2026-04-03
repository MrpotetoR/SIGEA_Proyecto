<x-panel title="Constancias" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Generar constancia --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-4">Generar constancia</h3>
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
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Tipo de constancia *</label>
                    <select name="tipo" required
                            class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <option value="estudio">De estudio</option>
                        <option value="calificaciones">De calificaciones</option>
                        <option value="comportamiento">De comportamiento</option>
                        <option value="servicio_social">Servicio social</option>
                        <option value="cultural">Actividades culturales</option>
                    </select>
                </div>
                <button type="submit"
                        class="w-full bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    Generar PDF
                </button>
            </form>
        </div>

        {{-- Historial --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 flex flex-col min-h-0" style="max-height: calc(100vh - 220px);">
            <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between flex-shrink-0">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300">Constancias emitidas</h3>
                <form method="GET" class="flex gap-2">
                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                           placeholder="Matrícula..."
                           class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-sm w-32 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    <button type="submit" class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 hover:bg-gray-200 px-3 py-1.5 rounded-lg text-sm text-gray-700">Buscar</button>
                </form>
            </div>
            <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left">Alumno</th>
                        <th class="px-4 py-3 text-center">Tipo</th>
                        <th class="px-4 py-3 text-center">Fecha</th>
                        <th class="px-4 py-3 text-center">Emitida por</th>
                        <th class="px-4 py-3 text-center">PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($constancias as $c)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3">
                                <p class="font-medium dark:text-gray-200">{{ $c->alumno?->nombre_completo }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-400">{{ $c->alumno?->matricula }}</p>
                            </td>
                            <td class="px-4 py-3 text-center capitalize dark:text-gray-300">{{ str_replace('_', ' ', $c->tipo) }}</td>
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">{{ $c->fecha_emision?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">{{ $c->generadaPor?->name }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('servicios.constancias.pdf', $c) }}"
                                   class="text-[#0606F0] dark:text-blue-400 hover:underline text-xs font-medium">Descargar</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 dark:text-gray-400">Sin constancias emitidas.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            @if($constancias instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="px-4 py-3 border-t dark:border-gray-700 flex-shrink-0">{{ $constancias->links() }}</div>
            @endif
        </div>
    </div>
</x-panel>
