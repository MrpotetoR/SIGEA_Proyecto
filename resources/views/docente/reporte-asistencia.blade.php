<x-panel title="Reporte Asistencia" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900">Reporte de Asistencia</h1>

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

    @if($reporte && $reporte->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">Alumno</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase">Presentes</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase">Ausentes</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase">Justificadas</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase">% Asistencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($reporte as $r)
                        @php
                            $total = $r->presentes + $r->ausentes + $r->justificadas;
                            $pct = $total > 0 ? round(($r->presentes + $r->justificadas) / $total * 100) : 0;
                            $pctColor = $pct >= 80 ? 'text-emerald-600 bg-emerald-50' : ($pct >= 60 ? 'text-amber-600 bg-amber-50' : 'text-red-600 bg-red-50');
                        @endphp
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-[13px] font-medium text-gray-800">{{ $r->alumno }}</td>
                            <td class="px-5 py-3 text-[13px] text-center text-emerald-600 font-medium">{{ $r->presentes }}</td>
                            <td class="px-5 py-3 text-[13px] text-center text-red-500 font-medium">{{ $r->ausentes }}</td>
                            <td class="px-5 py-3 text-[13px] text-center text-amber-500 font-medium">{{ $r->justificadas }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-[12px] font-bold {{ $pctColor }}">{{ $pct }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif(request('grupo'))
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-[14px] text-gray-400">Sin datos de asistencia para este grupo.</p>
        </div>
    @endif

</div>

</x-panel>
