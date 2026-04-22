<x-panel title="Horas ACUDE" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    {{-- Resumen de total acumulado --}}
    <div class="grid grid-cols-1 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 inline-flex items-center gap-1.5">
                    <x-icon name="sparkles" class="w-4 h-4 text-indigo-600" /> Horas ACUDE acumuladas
                </h3>
                <span class="text-2xl font-bold text-indigo-700 dark:text-indigo-300">{{ (int) $totalHoras }}</span>
            </div>
            @php $pct = $limite > 0 ? min(100, ($totalHoras / $limite) * 100) : 0; @endphp
            <div class="rainbow-track h-3 rainbow-glow">
                <div class="rainbow-bar" style="width: {{ $pct }}%"></div>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ (int) $totalHoras }} / {{ $limite }} horas (tope institucional)</p>
        </div>
    </div>

    {{-- Tabla de registros --}}
    @if($registros->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow flex flex-col min-h-0" style="max-height: calc(100vh - 280px);">
            <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-blue-50 dark:bg-gray-700/50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Descripción</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Horas</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($registros as $registro)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $registro->descripcion ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ (int) $registro->horas_acumuladas }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <td class="px-6 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</td>
                        <td class="px-4 py-2 text-center text-sm font-bold text-gray-800 dark:text-gray-200">
                            {{ (int) $totalHoras }}
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
