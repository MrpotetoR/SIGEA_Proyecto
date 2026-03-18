<x-panel title="Reporte Rendimiento" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900">Reporte de Rendimiento</h1>

    {{-- Filtro --}}
    <form method="GET" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-end gap-4">
            <div class="flex-1">
                <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block">Grupo</label>
                <select name="grupo" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-violet-300 outline-none">
                    <option value="">Selecciona un grupo</option>
                    @foreach($grupos as $grupoId => $horarios)
                        @php $g = $horarios->first()->grupo; @endphp
                        <option value="{{ $g->id_grupo }}" {{ request('grupo') == $g->id_grupo ? 'selected' : '' }}>
                            {{ $g->clave_grupo }} - {{ $horarios->map(fn($h) => $h->materia->nombre_materia)->unique()->join(', ') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl text-[13px] font-medium hover:bg-gray-700 transition-colors">
                Generar
            </button>
        </div>
    </form>

    @if(!empty($reporte))
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">Alumno</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase">Promedio</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase">Semáforo</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase">Estatus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($reporte as $r)
                        @php
                            $aprobado = $r['aprobado'];
                            $semaforo = $r['nivel_semaforo'] ?? 'verde';
                            $semaforoColor = match($semaforo) {
                                'rojo'     => 'bg-red-100 text-red-600',
                                'amarillo' => 'bg-amber-100 text-amber-600',
                                default    => 'bg-emerald-100 text-emerald-600',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-[13px] font-medium text-gray-800">{{ $r['alumno']->nombre_completo }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="font-bold text-[13px] {{ $aprobado ? 'text-emerald-600' : 'text-red-500' }}">
                                    {{ number_format($r['promedio'], 1) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $semaforoColor }}">
                                    {{ ucfirst($semaforo) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $aprobado ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500' }}">
                                    {{ $aprobado ? 'Aprobado' : 'Reprobado' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif(request('grupo'))
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-[14px] text-gray-400">Sin datos de calificaciones para este grupo.</p>
        </div>
    @endif

</div>

</x-panel>
