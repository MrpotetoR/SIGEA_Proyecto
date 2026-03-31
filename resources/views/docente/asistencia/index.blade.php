<x-panel title="Asistencia" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Pasar Asistencia</h1>
    <p class="text-[13px] text-gray-400 dark:text-gray-500">Selecciona un grupo para registrar la asistencia del dia.</p>

    @if($grupos->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($grupos as $grupoId => $horarios)
                @php $g = $horarios->first()->grupo; @endphp
                <a href="{{ route('docente.asistencia.show', $g->id_grupo) }}"
                   class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-gray-200 dark:hover:border-gray-600 transition-all group">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-[11px] font-bold text-emerald-700 dark:text-emerald-300">
                            {{ $g->clave_grupo }}
                        </div>
                        <div>
                            <p class="text-[14px] font-bold text-gray-800 dark:text-gray-200 group-hover:text-violet-700 dark:group-hover:text-violet-400 transition-colors">{{ $g->clave_grupo }}</p>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ $horarios->map(fn($h) => $h->materia->nombre_materia)->unique()->join(', ') }}</p>
                        </div>
                    </div>
                    <div class="text-[11px] font-medium text-violet-600 dark:text-indigo-400">Registrar asistencia &rarr;</div>
                </a>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-[14px] text-gray-400 dark:text-gray-500">Sin grupos asignados.</p>
        </div>
    @endif

</div>

</x-panel>
