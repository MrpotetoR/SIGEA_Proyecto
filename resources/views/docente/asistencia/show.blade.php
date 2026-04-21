<x-panel title="Asistencia - {{ $grupo->clave_grupo }}" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

@php
    $yaRegistrado = $asistencias->isNotEmpty();
    $fechaCarbon = \Carbon\Carbon::parse($fecha);
@endphp

<div class="space-y-5">

    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Asistencia: {{ $grupo->clave_grupo }}</h1>
            <p class="text-[13px] text-gray-400 dark:text-gray-500 mt-1">{{ $horario?->materia?->nombre_materia ?? 'Materia' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('docente.asistencia.historial', $grupo->id_grupo) }}"
               class="text-[12px] font-medium text-sky-700 dark:text-sky-400 bg-sky-50 dark:bg-sky-900/30 px-3 py-1.5 rounded-xl hover:bg-sky-100 dark:hover:bg-sky-900/50 transition-colors inline-flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Ver historial
            </a>
            <a href="{{ route('docente.asistencia') }}"
               class="text-[12px] font-medium text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">&larr; Volver</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 dark:bg-green-900/30 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-green-300 px-4 py-3 rounded-2xl text-[13px]">{{ session('success') }}</div>
    @endif

    {{-- Selector de fecha + banner de estado --}}
    <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm p-5">
        <form method="GET" action="{{ route('docente.asistencia.show', $grupo->id_grupo) }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1 block">Fecha</label>
                <input type="date" name="fecha" value="{{ $fecha }}" max="{{ today()->toDateString() }}"
                       onchange="this.form.submit()"
                       class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2 text-[13px] focus:ring-2 focus:ring-sky-300 outline-none">
            </div>
            @if($yaRegistrado)
                <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-amber-700 dark:text-amber-300 text-[12px]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    Editando registro del {{ $fechaCarbon->isoFormat('dddd D [de] MMMM') }}
                </div>
            @else
                <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-700 text-sky-700 dark:text-sky-300 text-[12px]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Nuevo registro para {{ $fechaCarbon->isoFormat('dddd D [de] MMMM') }}
                </div>
            @endif
        </form>
    </div>

    <form method="POST" action="{{ route('docente.asistencia.store', $grupo->id_grupo) }}">
        @csrf
        <input type="hidden" name="id_grupo" value="{{ $grupo->id_grupo }}">
        <input type="hidden" name="fecha" value="{{ $fecha }}">
        <input type="hidden" name="id_horario" value="{{ $horario?->id_horario }}">

        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm flex flex-col min-h-0" style="max-height: calc(100vh - 340px);">
            <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0 z-10">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">#</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Alumno</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Matricula</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Estatus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($alumnos as $i => $alumno)
                        @php $asistencia = $asistencias[$alumno->id_alumno] ?? null; @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-3 text-[12px] text-gray-400 dark:text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 text-[13px] font-medium text-gray-800 dark:text-gray-200">{{ $alumno->nombre_completo }}</td>
                            <td class="px-5 py-3 text-[12px] text-gray-500 dark:text-gray-400 font-mono">{{ $alumno->matricula }}</td>
                            <td class="px-5 py-3">
                                <div class="flex justify-center gap-2">
                                    @foreach(['presente' => 'P', 'ausente' => 'A', 'retardo' => 'R'] as $status => $label)
                                        @php
                                            $checked = ($asistencia ?? 'presente') === $status;
                                            $colorMap = [
                                                'presente' => 'peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500',
                                                'ausente'  => 'peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500',
                                                'retardo'  => 'peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500',
                                            ];
                                        @endphp
                                        <label class="cursor-pointer">
                                            <input type="radio" name="asistencia[{{ $alumno->id_alumno }}]" value="{{ $status }}" {{ $checked ? 'checked' : '' }} class="peer hidden">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg border-2 border-gray-200 dark:border-gray-600 text-[11px] font-bold text-gray-400 dark:text-gray-500 transition-all {{ $colorMap[$status] }}">
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
        </div>

        <div class="flex items-center justify-between mt-4 gap-3 flex-wrap">
            {{-- Leyenda --}}
            <div class="flex gap-4 text-[11px] text-gray-500 dark:text-gray-400">
                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-500"></span> Presente</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-500"></span> Ausente</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-amber-500"></span> Retardo</span>
            </div>
            <button type="submit" class="bg-[#0606F0] dark:bg-[#0606F0] text-white px-6 py-2.5 rounded-xl text-[13px] font-medium hover:bg-[#04276B] dark:hover:bg-blue-400 transition-colors">
                {{ $yaRegistrado ? 'Actualizar Asistencia' : 'Guardar Asistencia' }}
            </button>
        </div>
    </form>

</div>

</x-panel>
