<x-panel title="Horas ACUDE" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    {{-- Resumen de totales --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700">🎭 Horas Culturales</h3>
                <span class="text-2xl font-bold text-indigo-700">{{ $totalCultural }}</span>
            </div>
            @php $pctCultural = min(100, ($totalCultural / 30) * 100); @endphp
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-indigo-500 h-3 rounded-full transition-all" style="width: {{ $pctCultural }}%"></div>
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ $totalCultural }} / 30 horas requeridas</p>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700">⚽ Horas Deportivas</h3>
                <span class="text-2xl font-bold text-green-700">{{ $totalDeportiva }}</span>
            </div>
            @php $pctDeportiva = min(100, ($totalDeportiva / 30) * 100); @endphp
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-green-500 h-3 rounded-full transition-all" style="width: {{ $pctDeportiva }}%"></div>
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ $totalDeportiva }} / 30 horas requeridas</p>
        </div>

    </div>

    {{-- Tabla de registros --}}
    @if($registros->isNotEmpty())
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Horas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($registros as $registro)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $registro->tipo === 'cultural' ? 'bg-indigo-100 text-indigo-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $registro->tipo === 'cultural' ? '🎭 Cultural' : '⚽ Deportiva' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $registro->descripcion ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900">
                                {{ $registro->horas_acumuladas }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="2" class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</td>
                        <td class="px-4 py-2 text-center text-sm font-bold text-gray-800">
                            {{ $totalCultural + $totalDeportiva }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @else
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400">
            Sin registros de horas ACUDE.
        </div>
    @endif

</x-panel>
