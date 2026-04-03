<x-panel title="Reporte Asistencia" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Reporte de Asistencia</h1>

    {{-- Filtro --}}
    <form method="GET" class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-end gap-4">
            <div class="flex-1">
                <label class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1 block">Grupo</label>
                <select name="grupo" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-sky-300 outline-none">
                    <option value="">Selecciona un grupo</option>
                    @foreach($grupos as $grupoId => $horarios)
                        @php $g = $horarios->first()->grupo; @endphp
                        <option value="{{ $g->id_grupo }}" {{ request('grupo') == $g->id_grupo ? 'selected' : '' }}>
                            {{ $g->clave_grupo }} - {{ $horarios->map(fn($h) => $h->materia->nombre_materia)->unique()->join(', ') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-[#0606F0] dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-5 py-2.5 rounded-xl text-[13px] font-medium hover:bg-[#04276B] transition-colors">
                Generar
            </button>
        </div>
    </form>

    @if(!empty($reporte))
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm flex flex-col min-h-0" style="max-height: calc(100vh - 280px);">
            <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0 z-10">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Alumno</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Presentes</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Faltas</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Justificadas</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">% Asistencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($reporte as $r)
                        @php
                            $pct = $r['porcentaje'];
                            $pctColor = $pct >= 80 ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-300' : ($pct >= 60 ? 'text-amber-600 bg-amber-50 dark:bg-amber-900/30 dark:text-amber-300' : 'text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-400');
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-3 text-[13px] font-medium text-gray-800 dark:text-gray-200">{{ $r['alumno']->nombre_completo }}</td>
                            <td class="px-5 py-3 text-[13px] text-center text-emerald-600 dark:text-emerald-400 font-medium">{{ $r['presentes'] }}</td>
                            <td class="px-5 py-3 text-[13px] text-center text-red-500 dark:text-red-400 font-medium">{{ $r['faltas'] }}</td>
                            <td class="px-5 py-3 text-[13px] text-center text-amber-500 dark:text-amber-400 font-medium">{{ $r['justificadas'] }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-[12px] font-bold {{ $pctColor }}">{{ $pct }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    @elseif(request('grupo'))
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-[14px] text-gray-400 dark:text-gray-500">Sin datos de asistencia para este grupo.</p>
        </div>
    @endif

</div>

</x-panel>
