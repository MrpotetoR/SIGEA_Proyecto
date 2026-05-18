<x-panel title="Reportes Consolidados" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Reportes Institucionales</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Totales consolidados de Universidad y Bachillerato. Estos numeros bypasean
            el filtro por contexto, asi que reflejan la institucion completa.
        </p>
    </div>

    @php
        $totalAlumnos     = array_sum($alumnosPorNivel);
        $totalActivos     = array_sum($alumnosActivosPorNivel);
        $totalDocentes    = array_sum($docentesPorNivel);
        $totalGrupos      = array_sum($gruposPorNivel);
        $totalMaterias    = array_sum($materiasPorNivel);
    @endphp

    {{-- KPIs principales --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        {{-- Alumnos --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Alumnos</span>
                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalAlumnos }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $totalActivos }} activos</p>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-1.5">
                <div class="flex items-center justify-between text-[12px]">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-[#0606F0]"></span>
                        <span class="text-gray-600 dark:text-gray-400">Universidad</span>
                    </span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $alumnosPorNivel['universidad'] }}</span>
                </div>
                <div class="flex items-center justify-between text-[12px]">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">Bachillerato</span>
                    </span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $alumnosPorNivel['bachillerato'] }}</span>
                </div>
            </div>
        </div>

        {{-- Docentes --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Docentes</span>
                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalDocentes }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">en plantilla</p>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-1.5">
                <div class="flex items-center justify-between text-[12px]">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-[#0606F0]"></span>
                        <span class="text-gray-600 dark:text-gray-400">Universidad</span>
                    </span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $docentesPorNivel['universidad'] }}</span>
                </div>
                <div class="flex items-center justify-between text-[12px]">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">Bachillerato</span>
                    </span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $docentesPorNivel['bachillerato'] }}</span>
                </div>
            </div>
        </div>

        {{-- Grupos + Materias --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Estructura</span>
                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14-7H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z"/>
                </svg>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalGrupos }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">grupos</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalMaterias }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">materias</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-1.5">
                <div class="flex items-center justify-between text-[12px]">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-[#0606F0]"></span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $totalCarreras }} carreras</span>
                    </span>
                </div>
                <div class="flex items-center justify-between text-[12px]">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $totalPlanes }} planes bachi</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Detalle por carrera y por plan --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top 10 Carreras (Universidad) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2.5 h-2.5 rounded-full bg-[#0606F0]"></span>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Top 10 Carreras — Universidad</h3>
            </div>
            <div class="space-y-2">
                @forelse($alumnosPorCarrera as $c)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $c->nombre_carrera }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 font-mono">{{ $c->clave_carrera }}</p>
                        </div>
                        <span class="text-lg font-bold text-[#0606F0] dark:text-blue-400">{{ $c->alumnos_count }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">Sin carreras registradas.</p>
                @endforelse
            </div>
        </div>

        {{-- Planes de Bachillerato --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Planes — Bachillerato</h3>
            </div>
            <div class="space-y-2">
                @forelse($alumnosPorPlan as $p)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $p->nombre_plan }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 font-mono">{{ $p->clave_plan }} — {{ $p->num_semestres }} semestres</p>
                        </div>
                        <span class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ $p->alumnos_count }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">Sin planes registrados.</p>
                @endforelse
            </div>
        </div>
    </div>

    @if($cicloActivo)
        <div class="mt-6 text-[11px] text-gray-400 dark:text-gray-500 text-center">
            Datos al corte del ciclo activo: <span class="font-semibold">{{ $cicloActivo->nombre }}</span>
        </div>
    @endif
</x-panel>
