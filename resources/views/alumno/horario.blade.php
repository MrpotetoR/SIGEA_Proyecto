<x-panel title="Mi Horario" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-indigo-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Hora</th>
                        @foreach($dias as $dia)
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                {{ ucfirst($dia) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                    @php
                        $horas = ['07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00'];
                    @endphp
                    @foreach($horas as $hora)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono whitespace-nowrap">{{ $hora }}</td>
                            @foreach($dias as $dia)
                                <td class="px-4 py-3 text-center">
                                    @foreach($horario[$dia] ?? [] as $clase)
                                        @if(\Carbon\Carbon::parse($clase->hora_inicio)->format('H:i') === $hora)
                                            <div class="bg-indigo-100 rounded p-2 text-xs">
                                                <p class="font-semibold text-indigo-800">{{ $clase->materia?->nombre_materia ?? 'Sin materia' }}</p>
                                                <p class="text-indigo-600">{{ $clase->docente?->nombre_completo ?? 'Sin docente' }}</p>
                                                <p class="text-indigo-400">{{ \Carbon\Carbon::parse($clase->hora_inicio)->format('H:i') }}-{{ \Carbon\Carbon::parse($clase->hora_fin)->format('H:i') }}</p>
                                            </div>
                                        @endif
                                    @endforeach
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-panel>
