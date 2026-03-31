<x-panel title="Overview" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    {{-- Saludo --}}
    <div>
        <h1 class="text-[26px] font-bold text-gray-900 dark:text-gray-100 leading-tight">
            @php
                $hora = now()->hour;
                $saludo = $hora < 12 ? 'Buenos dias' : ($hora < 18 ? 'Buenas tardes' : 'Buenas noches');
            @endphp
            {{ $saludo }}, {{ $docente?->nombre ?? auth()->user()->name }}
        </h1>
        <p class="text-[13px] text-gray-400 dark:text-gray-500 mt-1">Resumen de tu actividad docente y grupos activos.</p>
    </div>

    {{-- Cards de resumen --}}
    <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700">
            <div class="p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-lg bg-violet-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </span>
                    <span class="text-[13px] font-semibold text-gray-700 dark:text-gray-300">Grupos Activos</span>
                </div>
                <p class="text-[22px] font-bold text-gray-900 dark:text-gray-100 leading-none">{{ $grupos->count() }}</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1.5">{{ $ciclo?->nombre ?? 'Sin ciclo activo' }}</p>
            </div>

            <div class="p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </span>
                    <span class="text-[13px] font-semibold text-gray-700 dark:text-gray-300">Especialidad</span>
                </div>
                <p class="text-[16px] font-bold text-gray-900 dark:text-gray-100 leading-none">{{ $docente?->especialidad ?? 'No asignada' }}</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1.5">{{ $docente?->horas_contrato ?? 0 }} hrs/contrato</p>
            </div>

            <div class="p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </span>
                    <span class="text-[13px] font-semibold text-gray-700 dark:text-gray-300">Evaluacion</span>
                </div>
                <p class="text-[22px] font-bold text-gray-900 dark:text-gray-100 leading-none">{{ $docente?->promedio_evaluacion ?? '0' }}</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1.5">Promedio de evaluaciones</p>
            </div>
        </div>
    </div>

    {{-- Grid: Grupos + Noticias --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Grupos del ciclo --}}
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between px-5 pt-5 pb-3">
                <h2 class="text-[15px] font-bold text-gray-800 dark:text-gray-200">Grupos del Ciclo</h2>
                <a href="{{ route('docente.grupos') }}" class="text-[11px] font-medium text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 px-2.5 py-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">Ver todos &rarr;</a>
            </div>
            <div class="px-5 pb-5">
                @if($grupos->isNotEmpty())
                    <div class="space-y-1">
                        @php $colors = ['bg-violet-100 text-violet-700', 'bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-700', 'bg-sky-100 text-sky-700', 'bg-rose-100 text-rose-700']; @endphp
                        @foreach($grupos as $grupoId => $horariosDel)
                            @php $g = $horariosDel->first()->grupo; $colorClass = $colors[$loop->index % count($colors)]; @endphp
                            <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50/70 dark:hover:bg-gray-700/50">
                                <div class="w-10 h-10 rounded-xl {{ explode(' ', $colorClass)[0] }} flex items-center justify-center text-[11px] font-bold {{ explode(' ', $colorClass)[1] }}">
                                    {{ $g->clave_grupo }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $horariosDel->map(fn($h) => $h->materia->nombre_materia)->unique()->join(', ') }}</p>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ $g->inscripciones_count ?? $g->inscripciones()->count() }} alumnos</p>
                                </div>
                                <a href="{{ route('docente.asistencia.show', $g->id_grupo) }}" class="text-[11px] font-medium text-violet-600 hover:text-violet-800 px-2 py-1 rounded-lg hover:bg-violet-50 dark:hover:bg-violet-900/30">Pasar lista</a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-[13px] text-gray-400 dark:text-gray-500">Sin grupos asignados en el ciclo actual.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Noticias --}}
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between px-5 pt-5 pb-3">
                <h2 class="text-[15px] font-bold text-gray-800 dark:text-gray-200">Noticias Recientes</h2>
                <a href="{{ route('docente.noticias') }}" class="text-[11px] font-medium text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 px-2.5 py-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">Ver todas &rarr;</a>
            </div>
            <div class="px-5 pb-5">
                @if($noticias->isNotEmpty())
                    <div class="space-y-1">
                        @foreach($noticias as $noticia)
                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50/70 dark:hover:bg-gray-700/50">
                                <div class="w-2 h-2 rounded-full bg-indigo-400 mt-1.5 flex-shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $noticia->titulo }}</p>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $noticia->fecha_publicacion->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-[13px] text-gray-400 dark:text-gray-500">Sin noticias recientes.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

</x-panel>
