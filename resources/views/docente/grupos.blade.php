<x-panel title="Mis Grupos" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <div class="flex items-center justify-between">
        <h1 class="text-[22px] font-bold text-gray-900">Mis Grupos</h1>
        <span class="text-[12px] font-medium text-gray-400 bg-gray-100 px-3 py-1.5 rounded-full">{{ $ciclo?->nombre ?? 'Sin ciclo' }}</span>
    </div>

    @if($grupos->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @php $colors = ['border-violet-200 bg-violet-50/30', 'border-emerald-200 bg-emerald-50/30', 'border-amber-200 bg-amber-50/30', 'border-sky-200 bg-sky-50/30', 'border-rose-200 bg-rose-50/30']; @endphp
            @foreach($grupos as $grupoId => $horariosDel)
                @php $grupo = $horariosDel->first()->grupo; @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-gray-900 text-white flex items-center justify-center text-[11px] font-bold">
                                {{ $grupo->clave_grupo }}
                            </div>
                            <div>
                                <p class="text-[14px] font-bold text-gray-800">{{ $grupo->clave_grupo }}</p>
                                <p class="text-[11px] text-gray-400">{{ $grupo->carrera?->nombre_carrera ?? 'Sin carrera' }}</p>
                            </div>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-[12px]">
                                <span class="text-gray-400">Cuatrimestre</span>
                                <span class="font-medium text-gray-700">{{ $grupo->cuatrimestre }}°</span>
                            </div>
                            <div class="flex justify-between text-[12px]">
                                <span class="text-gray-400">Alumnos inscritos</span>
                                <span class="font-medium text-gray-700">{{ $grupo->inscripciones->count() }}</span>
                            </div>
                            <div class="flex justify-between text-[12px]">
                                <span class="text-gray-400">Materias</span>
                                <span class="font-medium text-gray-700">{{ $horariosDel->map(fn($h) => $h->materia->nombre_materia)->unique()->join(', ') }}</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('docente.asistencia.show', $grupo->id_grupo) }}"
                               class="flex-1 text-center text-[11px] font-medium bg-gray-900 text-white px-3 py-2 rounded-xl hover:bg-gray-700 transition-colors">
                                Asistencia
                            </a>
                            <a href="{{ route('docente.calificaciones.show', $grupo->id_grupo) }}"
                               class="flex-1 text-center text-[11px] font-medium bg-gray-100 text-gray-700 px-3 py-2 rounded-xl hover:bg-gray-200 transition-colors">
                                Calificaciones
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-[14px] text-gray-400">Sin grupos asignados en el ciclo actual.</p>
        </div>
    @endif

</div>

</x-panel>
