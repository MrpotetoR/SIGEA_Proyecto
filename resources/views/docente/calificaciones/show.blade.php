<x-panel title="Calificaciones - {{ $grupo->clave_grupo }}" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Calificaciones: {{ $grupo->clave_grupo }}</h1>
            <p class="text-[13px] text-gray-400 dark:text-gray-500 mt-1">{{ $horario?->materia?->nombre_materia ?? 'Materia' }} &mdash; Parcial {{ $parcial }}</p>
        </div>
        <div class="flex items-center gap-2">
            @for($p = 1; $p <= 3; $p++)
                <a href="{{ route('docente.calificaciones.show', ['grupo' => $grupo->id_grupo, 'parcial' => $p]) }}"
                   class="px-3 py-1.5 rounded-xl text-[12px] font-medium transition-colors {{ $parcial == $p ? 'bg-[#0606F0] dark:bg-[#0606F0] text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Parcial {{ $p }}
                </a>
            @endfor
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 dark:bg-green-900/30 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-green-300 px-4 py-3 rounded-2xl text-[13px]">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('docente.calificaciones.store', $grupo->id_grupo) }}">
        @csrf
        <input type="hidden" name="parcial" value="{{ $parcial }}">
        <input type="hidden" name="id_materia" value="{{ $horario?->id_materia }}">
        <input type="hidden" name="id_ciclo" value="{{ $horario?->grupo?->id_ciclo }}">

        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm flex flex-col min-h-0" style="max-height: calc(100vh - 280px);">
            <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0 z-10">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">#</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Alumno</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Matricula</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Calificacion</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($alumnos as $i => $alumno)
                        @php $cal = $calificaciones[$alumno->id_alumno] ?? null; @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-3 text-[12px] text-gray-400 dark:text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 text-[13px] font-medium text-gray-800 dark:text-gray-200">{{ $alumno->nombre_completo }}</td>
                            <td class="px-5 py-3 text-[12px] text-gray-500 dark:text-gray-400 font-mono">{{ $alumno->matricula }}</td>
                            <td class="px-5 py-3">
                                <div class="flex justify-center">
                                    <input type="hidden" name="calificaciones[{{ $i }}][id_alumno]" value="{{ $alumno->id_alumno }}">
                                    <input type="hidden" name="calificaciones[{{ $i }}][id_materia]" value="{{ $horario?->id_materia }}">
                                    <input type="hidden" name="calificaciones[{{ $i }}][id_ciclo]" value="{{ $horario?->grupo?->id_ciclo }}">
                                    <input type="hidden" name="calificaciones[{{ $i }}][parcial]" value="{{ $parcial }}">
                                    <input type="number"
                                           name="calificaciones[{{ $i }}][calificacion]"
                                           value="{{ $cal ?? '' }}"
                                           min="0" max="10" step="0.1"
                                           placeholder="0.0"
                                           class="w-20 text-center text-[13px] font-medium border border-gray-200 dark:border-gray-600 rounded-xl px-2 py-2 focus:ring-2 focus:ring-sky-300 dark:focus:ring-sky-700 focus:border-sky-400 outline-none bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>

        <div class="flex justify-between items-center mt-4">
            <a href="{{ route('docente.calificaciones') }}" class="text-[12px] text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">&larr; Volver a grupos</a>
            <button type="submit" class="bg-[#0606F0] dark:bg-[#0606F0] text-white px-6 py-2.5 rounded-xl text-[13px] font-medium hover:bg-[#04276B] dark:hover:bg-blue-400 transition-colors">
                Guardar Calificaciones
            </button>
        </div>
    </form>

</div>

</x-panel>
