<x-panel title="Overview" panelNombre="Panel Alumno">
<x-slot name="nav">@include('partials.alumno-nav')</x-slot>

<div class="space-y-5">

        {{-- Saludo --}}
        <div>
            <h1 class="text-[26px] font-bold text-gray-900 dark:text-gray-100 leading-tight">
                @php
                    $hora = now()->hour;
                    $saludo = $hora < 12 ? 'Buenos días' : ($hora < 18 ? 'Buenas tardes' : 'Buenas noches');
                @endphp
                {{ $saludo }}, {{ $alumno?->nombre ?? auth()->user()->name }}
            </h1>
            <p class="text-[13px] text-gray-400 mt-1">Aquí puedes ver tu estado académico y actividad reciente.</p>
        </div>

        {{-- Semáforo académico --}}
        @if($alumno && $semaforo)
            @php
                $nivel = $semaforo->nivel;
                $sStyle = match($nivel) {
                    'verde'    => ['bg' => 'bg-emerald-50/70', 'border' => 'border-emerald-200/60', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
                    'amarillo' => ['bg' => 'bg-amber-50/70',   'border' => 'border-amber-200/60',   'text' => 'text-amber-700',   'dot' => 'bg-amber-500'],
                    'rojo'     => ['bg' => 'bg-red-50/70',     'border' => 'border-red-200/60',     'text' => 'text-red-700',     'dot' => 'bg-red-500'],
                    default    => ['bg' => 'bg-gray-50/70',    'border' => 'border-gray-200/60',    'text' => 'text-gray-700',    'dot' => 'bg-gray-500'],
                };
            @endphp
            <div class="rounded-2xl border {{ $sStyle['bg'] }} {{ $sStyle['border'] }} p-4 flex items-center gap-4 card-hover">
                <div class="w-10 h-10 rounded-xl {{ $sStyle['dot'] }} flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-[14px] {{ $sStyle['text'] }}">Semáforo Académico: {{ ucfirst($nivel) }}</p>
                    <p class="text-[12px] text-gray-500 dark:text-gray-400 mt-0.5">
                        Promedio: <strong class="text-gray-700 dark:text-gray-200">{{ $semaforo->promedio_calificaciones }}</strong>
                        <span class="mx-1.5 text-gray-300 dark:text-gray-600">|</span>
                        Asistencia: <strong class="text-gray-700 dark:text-gray-200">{{ $semaforo->porcentaje_asistencia }}%</strong>
                    </p>
                </div>
            </div>
        @endif

        {{-- Cards de resumen (estilo mockup: borde sutil, 3 columnas con datos e ícono) --}}
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700">
                {{-- Progress / Matrícula --}}
                <div class="p-5 card-hover">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                                </svg>
                            </span>
                            <span class="text-[13px] font-semibold text-gray-700 dark:text-gray-200">Matrícula</span>
                        </div>
                    </div>
                    <p class="text-[22px] font-bold text-gray-900 dark:text-gray-100 leading-none">{{ $alumno?->matricula ?? '—' }}</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1.5">{{ $alumno?->carrera?->nombre_carrera ?? 'Sin carrera asignada' }}</p>
                </div>

                {{-- Cuatrimestre --}}
                <div class="p-5 card-hover">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-sky-100 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </span>
                            <span class="text-[13px] font-semibold text-gray-700 dark:text-gray-200">Cuatrimestre</span>
                        </div>
                        <span class="text-[11px] text-gray-400 dark:text-gray-500">de 9</span>
                    </div>
                    <p class="text-[22px] font-bold text-gray-900 dark:text-gray-100 leading-none">{{ $alumno?->cuatrimestre_actual ?? '—' }}°</p>
                    <div class="mt-2.5 rainbow-track h-2 rainbow-glow">
                        @php $pct = min(100, (($alumno?->cuatrimestre_actual ?? 0) / 9) * 100); @endphp
                        <div class="rainbow-bar" style="width: {{ $pct }}%"></div>
                    </div>
                </div>

                {{-- Clases hoy --}}
                <div class="p-5 card-hover">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-sky-100 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <span class="text-[13px] font-semibold text-gray-700 dark:text-gray-200">Clases Hoy</span>
                        </div>
                    </div>
                    <p class="text-[22px] font-bold text-gray-900 dark:text-gray-100 leading-none">{{ $proximasClases->count() }}</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1.5 capitalize">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM') }}</p>
                </div>
            </div>
        </div>

        {{-- Grid: Clases + Noticias --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Clases de hoy --}}
            <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm card-hover">
                <div class="flex items-center justify-between px-5 pt-5 pb-3">
                    <h2 class="text-[15px] font-bold text-gray-800 dark:text-gray-200">Clases de Hoy</h2>
                    <a href="{{ route('alumno.horario') }}"
                       class="text-[11px] font-medium text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 px-2.5 py-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        Ver horario →
                    </a>
                </div>

                <div class="px-5 pb-5">
                    @if($proximasClases->isNotEmpty())
                        <div class="space-y-1">
                            @php
                                $colors = ['bg-sky-100 border-sky-300 text-sky-700', 'bg-emerald-100 border-emerald-300 text-emerald-700', 'bg-amber-100 border-amber-300 text-amber-700', 'bg-sky-100 border-sky-300 text-sky-700', 'bg-rose-100 border-rose-300 text-rose-700'];
                            @endphp
                            @foreach($proximasClases as $i => $clase)
                                @php $colorClass = $colors[$i % count($colors)]; @endphp
                                <div class="flex items-center gap-3 p-3 rounded-xl {{ explode(' ', $colorClass)[0] }}/40 border border-transparent hover:border-gray-200">
                                    <div class="w-8 h-8 rounded-lg {{ explode(' ', $colorClass)[0] }} flex items-center justify-center text-[11px] font-bold {{ explode(' ', $colorClass)[2] }}">
                                        {{ \Carbon\Carbon::parse($clase->hora_inicio)->format('H:i') }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $clase->materia->nombre_materia }}</p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 truncate">{{ $clase->docente->nombre_completo }}</p>
                                    </div>
                                    <span class="text-[10px] font-mono text-gray-400 dark:text-gray-500">
                                        {{ \Carbon\Carbon::parse($clase->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($clase->hora_fin)->format('H:i') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-gray-50 dark:bg-gray-700/50 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-[13px] text-gray-400 dark:text-gray-500">Sin clases programadas para hoy</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Noticias recientes --}}
            <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm card-hover">
                <div class="flex items-center justify-between px-5 pt-5 pb-3">
                    <h2 class="text-[15px] font-bold text-gray-800 dark:text-gray-200">Noticias Recientes</h2>
                    <a href="{{ route('alumno.noticias') }}"
                       class="text-[11px] font-medium text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 px-2.5 py-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        Ver todas →
                    </a>
                </div>

                <div class="px-5 pb-5">
                    @if($noticias->isNotEmpty())
                        <div class="space-y-1">
                            @foreach($noticias as $noticia)
                                <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50/70 dark:hover:bg-gray-700/50">
                                    <div class="w-2 h-2 rounded-full bg-blue-400 mt-1.5 flex-shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $noticia->titulo }}</p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $noticia->fecha_publicacion->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-gray-50 dark:bg-gray-700/50 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2"/>
                                </svg>
                            </div>
                            <p class="text-[13px] text-gray-400 dark:text-gray-500">Sin noticias recientes</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

</div>

</x-panel>
