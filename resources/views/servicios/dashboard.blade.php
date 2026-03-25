<x-panel title="Overview" panelNombre="Servicios Escolares">
<x-slot name="nav">@include('partials.servicios-nav')</x-slot>

<div class="space-y-5">

    {{-- Saludo --}}
    <div>
        <h1 class="text-[26px] font-bold text-gray-900 leading-tight">
            @php
                $hora = now()->hour;
                $saludo = $hora < 12 ? 'Buenos días' : ($hora < 18 ? 'Buenas tardes' : 'Buenas noches');
            @endphp
            {{ $saludo }}, {{ auth()->user()->name }}
        </h1>
        <p class="text-[13px] text-gray-400 mt-1">Panel de administración escolar — {{ now()->translatedFormat('l, j \d\e F \d\e Y') }}</p>
    </div>

    {{-- Alerta ciclo inactivo --}}
    @unless($stats['ciclo_activo'])
        <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-5 py-3.5 text-[13px]">
            <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-amber-700">No hay un ciclo escolar activo.</span>
            <a href="{{ route('servicios.ciclos.create') }}" class="ml-auto text-amber-700 font-semibold hover:underline">Crear ciclo →</a>
        </div>
    @endunless

    {{-- KPIs --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100">

            {{-- Alumnos activos --}}
            <div class="p-5 card-hover">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </span>
                    <span class="text-[13px] font-semibold text-gray-700">Alumnos Activos</span>
                </div>
                <p class="text-[28px] font-bold text-gray-900 leading-none">{{ $stats['total_alumnos'] }}</p>
                <a href="{{ route('servicios.alumnos.index') }}" class="text-[11px] text-indigo-500 hover:underline mt-1.5 block">Ver listado →</a>
            </div>

            {{-- Bajas temporales --}}
            <div class="p-5 card-hover">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </span>
                    <span class="text-[13px] font-semibold text-gray-700">Bajas Temporales</span>
                </div>
                <p class="text-[28px] font-bold text-gray-900 leading-none">{{ $stats['bajas_temporales'] }}</p>
                <a href="{{ route('servicios.alumnos.index', ['estatus' => 'baja_temporal']) }}" class="text-[11px] text-amber-500 hover:underline mt-1.5 block">Ver bajas →</a>
            </div>

            {{-- Docentes --}}
            <div class="p-5 card-hover">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-lg bg-violet-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </span>
                    <span class="text-[13px] font-semibold text-gray-700">Docentes</span>
                </div>
                <p class="text-[28px] font-bold text-gray-900 leading-none">{{ $stats['total_docentes'] }}</p>
                <a href="{{ route('servicios.docentes.index') }}" class="text-[11px] text-violet-500 hover:underline mt-1.5 block">Ver listado →</a>
            </div>

            {{-- Carreras --}}
            <div class="p-5 card-hover">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-lg bg-sky-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </span>
                    <span class="text-[13px] font-semibold text-gray-700">Carreras</span>
                </div>
                <p class="text-[28px] font-bold text-gray-900 leading-none">{{ $stats['total_carreras'] }}</p>
                <a href="{{ route('servicios.carreras.index') }}" class="text-[11px] text-sky-500 hover:underline mt-1.5 block">Ver carreras →</a>
            </div>

        </div>
    </div>

    {{-- Fila: Ciclo activo + Accesos rápidos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Ciclo escolar activo --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between px-5 pt-5 pb-3">
                <h2 class="text-[15px] font-bold text-gray-800">Ciclo Escolar</h2>
                <a href="{{ route('servicios.ciclos.index') }}"
                   class="text-[11px] font-medium text-gray-400 hover:text-gray-700 px-2.5 py-1 rounded-lg hover:bg-gray-50">
                    Ver todos →
                </a>
            </div>
            <div class="px-5 pb-5">
                @if($stats['ciclo_activo'])
                    @php
                        $ciclo = $stats['ciclo_activo'];
                        $inicio = \Carbon\Carbon::parse($ciclo->fecha_inicio);
                        $fin    = \Carbon\Carbon::parse($ciclo->fecha_fin);
                        $total  = $inicio->diffInDays($fin);
                        $transcurrido = $inicio->diffInDays(now());
                        $pct = min(100, $total > 0 ? round(($transcurrido / $total) * 100) : 0);
                    @endphp
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[18px] font-bold text-gray-900 leading-none">{{ $ciclo->nombre }}</p>
                            <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full mt-1 inline-block">Activo</span>
                        </div>
                    </div>
                    <div class="text-[12px] text-gray-400 flex justify-between mb-2">
                        <span>{{ $inicio->format('d/m/Y') }}</span>
                        <span>{{ $fin->format('d/m/Y') }}</span>
                    </div>
                    <div class="rainbow-track h-2 rainbow-glow">
                        <div class="rainbow-bar" style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1.5">{{ $pct }}% transcurrido · {{ max(0, now()->diffInDays($fin, false)) }} días restantes</p>
                @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-[13px] text-gray-400 mb-2">Sin ciclo activo</p>
                        <a href="{{ route('servicios.ciclos.create') }}"
                           class="text-[12px] font-semibold text-indigo-600 hover:underline">Crear ciclo escolar →</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Accesos rápidos --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm card-hover">
            <div class="px-5 pt-5 pb-3">
                <h2 class="text-[15px] font-bold text-gray-800">Accesos Rápidos</h2>
            </div>
            <div class="px-5 pb-5 grid grid-cols-2 gap-2">
                @php
                $accesos = [
                    ['route' => 'servicios.alumnos.create',  'label' => 'Nuevo Alumno',      'color' => 'bg-indigo-50 hover:bg-indigo-100 text-indigo-700',
                     'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'],
                    ['route' => 'servicios.docentes.create', 'label' => 'Nuevo Docente',     'color' => 'bg-violet-50 hover:bg-violet-100 text-violet-700',
                     'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'],
                    ['route' => 'servicios.noticias.create', 'label' => 'Publicar Noticia',  'color' => 'bg-sky-50 hover:bg-sky-100 text-sky-700',
                     'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                    ['route' => 'servicios.reportes',        'label' => 'Ver Reportes',       'color' => 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700',
                     'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ['route' => 'servicios.constancias',     'label' => 'Constancias',        'color' => 'bg-amber-50 hover:bg-amber-100 text-amber-700',
                     'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['route' => 'servicios.inscripciones',   'label' => 'Inscripciones',      'color' => 'bg-rose-50 hover:bg-rose-100 text-rose-700',
                     'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                ];
                @endphp
                @foreach($accesos as $a)
                    <a href="{{ route($a['route']) }}"
                       class="flex items-center gap-2.5 px-3.5 py-3 rounded-xl {{ $a['color'] }} transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $a['icon'] }}"/>
                        </svg>
                        <span class="text-[13px] font-semibold">{{ $a['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

    </div>

</div>
</x-panel>
