<x-panel title="Horas ACUDE" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    {{-- Resumen de totales --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">🎭 Horas Culturales</h3>
                <span class="text-2xl font-bold text-blue-700">{{ $totalCultural }}</span>
            </div>
            @php $pctCultural = min(100, ($totalCultural / 30) * 100); @endphp
            <div class="rainbow-track h-3 rainbow-glow">
                <div class="rainbow-bar" style="width: {{ $pctCultural }}%"></div>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ $totalCultural }} / 30 horas requeridas</p>
        </div>

        <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">⚽ Horas Deportivas</h3>
                <span class="text-2xl font-bold text-green-700">{{ $totalDeportiva }}</span>
            </div>
            @php $pctDeportiva = min(100, ($totalDeportiva / 30) * 100); @endphp
            <div class="rainbow-track h-3 rainbow-glow">
                <div class="rainbow-bar" style="width: {{ $pctDeportiva }}%"></div>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ $totalDeportiva }} / 30 horas requeridas</p>
        </div>

    </div>

    {{-- Tabla de registros --}}
    @if($registros->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow flex flex-col min-h-0" style="max-height: calc(100vh - 280px);">
            <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-blue-50 dark:bg-gray-700/50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Descripción</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Horas</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($registros as $registro)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $registro->tipo === 'cultural' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' }}">
                                    {{ $registro->tipo === 'cultural' ? '🎭 Cultural' : '⚽ Deportiva' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $registro->descripcion ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $registro->horas_acumuladas }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <td colspan="2" class="px-6 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</td>
                        <td class="px-4 py-2 text-center text-sm font-bold text-gray-800 dark:text-gray-200">
                            {{ $totalCultural + $totalDeportiva }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-12 text-center text-gray-400 dark:text-gray-500">
            Sin registros de horas ACUDE.
        </div>
    @endif

</x-panel>
