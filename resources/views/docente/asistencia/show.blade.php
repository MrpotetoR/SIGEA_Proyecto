<x-panel title="Asistencia - {{ $grupo->clave_grupo }}" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-[22px] font-bold text-gray-900">Asistencia: {{ $grupo->clave_grupo }}</h1>
            <p class="text-[13px] text-gray-400 mt-1">{{ $horario?->materia?->nombre_materia ?? 'Materia' }} &mdash; {{ $fecha }}</p>
        </div>
        <a href="{{ route('docente.asistencia') }}" class="text-[12px] font-medium text-gray-500 hover:text-gray-700 bg-gray-100 px-3 py-1.5 rounded-xl hover:bg-gray-200 transition-colors">&larr; Volver</a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-[13px]">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('docente.asistencia.store', $grupo->id_grupo) }}">
        @csrf
        <input type="hidden" name="id_grupo" value="{{ $grupo->id_grupo }}">
        <input type="hidden" name="fecha" value="{{ $fecha }}">
        <input type="hidden" name="id_horario" value="{{ $horario?->id_horario }}">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">Alumno</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">Matricula</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase">Estatus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($alumnos as $i => $alumno)
                        @php $asistencia = $asistencias[$alumno->id_alumno] ?? null; @endphp
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-[12px] text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 text-[13px] font-medium text-gray-800">{{ $alumno->nombre_completo }}</td>
                            <td class="px-5 py-3 text-[12px] text-gray-500 font-mono">{{ $alumno->matricula }}</td>
                            <td class="px-5 py-3">
                                <div class="flex justify-center gap-2">
                                    @foreach(['presente' => 'P', 'ausente' => 'A', 'justificada' => 'J'] as $status => $label)
                                        @php
                                            $checked = ($asistencia ?? 'presente') === $status;
                                            $colorMap = ['presente' => 'peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500', 'ausente' => 'peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500', 'justificada' => 'peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500'];
                                        @endphp
                                        <label class="cursor-pointer">
                                            <input type="radio" name="asistencia[{{ $alumno->id_alumno }}]" value="{{ $status }}" {{ $checked ? 'checked' : '' }} class="peer hidden">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg border-2 border-gray-200 text-[11px] font-bold text-gray-400 transition-all {{ $colorMap[$status] }}">
                                                {{ $label }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end mt-4">
            <button type="submit" class="bg-gray-900 text-white px-6 py-2.5 rounded-xl text-[13px] font-medium hover:bg-gray-700 transition-colors">
                Guardar Asistencia
            </button>
        </div>
    </form>

</div>

</x-panel>
