@php
/**
 * Sidebar del Gestor Escolar — dinamico segun contexto educativo.
 *
 * Variables compartidas por el middleware EstableceContextoEducativo:
 *   $contextoActual       string  'universidad' | 'bachillerato'
 *   $contextoDisponibles  array   niveles a los que el usuario tiene acceso
 *   $contextoColor        array   colores + label del contexto activo
 */

$esUniversidad  = ($contextoActual ?? 'universidad') === 'universidad';
$esBachillerato = ($contextoActual ?? null) === 'bachillerato';
$tieneAmbos     = count($contextoDisponibles ?? []) >= 2;

$sections = [
    [
        'label' => 'Principal',
        'visible' => true,
        'links' => [
            ['route' => 'gestor.dashboard', 'label' => 'Overview',  'match' => 'gestor.dashboard',
             'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['route' => 'gestor.perfil',    'label' => 'Mi Perfil', 'match' => 'gestor.perfil',
             'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ],
    ],
    [
        'label' => 'Alumnos',
        'visible' => true,
        'links' => [
            ['route' => 'gestor.alumnos.index',           'label' => 'Alumnos',           'match' => 'gestor.alumnos.*',
             'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['route' => 'gestor.historial-alumnos.index', 'label' => 'Historial Alumnos','match' => 'gestor.historial-alumnos.*',
             'icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'],
            ['route' => 'gestor.inscripciones',           'label' => 'Inscripciones',     'match' => 'gestor.inscripciones*',
             'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ['route' => 'gestor.constancias',             'label' => 'Constancias',       'match' => 'gestor.constancias*',
             'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['route' => 'gestor.servicio-social.index',   'label' => 'Servicio Social',   'match' => 'gestor.servicio-social.*',
             'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
        ],
    ],
    [
        'label' => 'Academico',
        'visible' => true,
        'links' => array_values(array_filter([
            ['route' => 'gestor.docentes.index',   'label' => 'Docentes',         'match' => 'gestor.docentes.*',
             'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
             'visible' => true],
            ['route' => 'gestor.carreras.index',   'label' => 'Carreras',         'match' => 'gestor.carreras.*',
             'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
             'visible' => $esUniversidad],
            ['route' => 'gestor.materias.index',   'label' => 'Materias',         'match' => 'gestor.materias.*',
             'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
             'visible' => true],
            ['route' => 'gestor.ciclos.index',     'label' => $esBachillerato ? 'Semestres' : 'Ciclos Escolares', 'match' => 'gestor.ciclos.*',
             'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
             'visible' => true],
            ['route' => 'gestor.plan-estudios',    'label' => 'Plan de Estudios', 'match' => 'gestor.plan-estudios',
             'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
             'visible' => $esUniversidad],
            ['route' => 'gestor.planes-bachillerato.index', 'label' => 'Planes Bachillerato', 'match' => 'gestor.planes-bachillerato.*',
             'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
             'visible' => $esBachillerato],
        ], fn($link) => $link['visible'] ?? true)),
    ],
    [
        'label' => 'Operacion',
        'visible' => true,
        'links' => [
            ['route' => 'gestor.grupos.index',   'label' => 'Grupos',   'match' => 'gestor.grupos.*',
             'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['route' => 'gestor.horarios.index', 'label' => 'Horarios', 'match' => 'gestor.horarios.*',
             'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ],
    ],
    [
        'label' => 'Tienda',
        'visible' => true,
        'links' => [
            ['route' => 'gestor.productos.index', 'label' => 'Productos', 'match' => 'gestor.productos.*',
             'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
            ['route' => 'gestor.pedidos.index', 'label' => 'Pedidos', 'match' => 'gestor.pedidos.*',
             'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
        ],
    ],
    [
        'label' => 'Reportes',
        'visible' => true,
        'links' => [
            ['route' => 'gestor.asistencia',          'label' => 'Asistencia',        'match' => 'gestor.asistencia',
             'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ['route' => 'gestor.indice-aprobacion',   'label' => 'Indice Aprobacion', 'match' => 'gestor.indice-aprobacion',
             'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['route' => 'gestor.evaluacion-docente',  'label' => 'Eval. Docentes',    'match' => 'gestor.evaluacion-docente',
             'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
            ['route' => 'gestor.reportes',            'label' => 'Reportes',          'match' => 'gestor.reportes',
             'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ],
    ],
    [
        'label' => 'Contenido',
        'visible' => true,
        'links' => [
            ['route' => 'gestor.noticias.index',   'label' => 'Noticias',   'match' => 'gestor.noticias.*',
             'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
            ['route' => 'gestor.documentos.index', 'label' => 'Documentos', 'match' => 'gestor.documentos.*',
             'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z'],
        ],
    ],
];

// Color del contexto activo para los pills y badges.
$colorActivo = $contextoColor ?? ['hex' => '#0606F0', 'label' => 'Universidad'];
@endphp

{{-- Selector de contexto educativo --}}
@if($tieneAmbos)
<div class="px-3 pb-3">
    <p class="text-[9px] font-semibold text-[#04276B]/40 dark:text-white/40 uppercase tracking-[0.12em] mb-1.5 px-1">
        Area de trabajo
    </p>
    <div class="bg-gray-100 dark:bg-gray-800/80 rounded-xl p-1 space-y-1">
        <form method="POST" action="{{ route('gestor.contexto.cambiar') }}">
            @csrf
            <input type="hidden" name="nivel" value="universidad">
            <button type="submit"
                    class="w-full text-[12.5px] font-semibold py-2 px-3 rounded-lg transition-all flex items-center gap-2.5
                           {{ $esUniversidad
                              ? 'bg-[#0606F0] text-white shadow-sm shadow-[#0606F0]/25'
                              : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700/50' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
                <span class="flex-1 text-left">Universidad</span>
                @if($esUniversidad)
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                @endif
            </button>
        </form>
        <form method="POST" action="{{ route('gestor.contexto.cambiar') }}">
            @csrf
            <input type="hidden" name="nivel" value="bachillerato">
            <button type="submit"
                    class="w-full text-[12.5px] font-semibold py-2 px-3 rounded-lg transition-all flex items-center gap-2.5
                           {{ $esBachillerato
                              ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/25'
                              : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700/50' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="flex-1 text-left">Bachillerato</span>
                @if($esBachillerato)
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                @endif
            </button>
        </form>
    </div>
</div>
@else
    {{-- Badge fijo cuando solo hay un nivel disponible --}}
    <div class="px-3 pb-3">
        <div class="text-[11px] uppercase tracking-wider font-bold {{ $esBachillerato ? 'text-amber-600 dark:text-amber-400' : 'text-[#0606F0] dark:text-blue-400' }} bg-gray-100 dark:bg-gray-800/80 rounded-lg py-2 text-center flex items-center justify-center gap-2">
            @if($esBachillerato)
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            @else
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
            @endif
            {{ $colorActivo['label'] }}
        </div>
    </div>
@endif

@foreach($sections as $section)
    @continue(!($section['visible'] ?? true) || empty($section['links']))
    <p class="px-3 pt-4 pb-1.5 text-[10px] font-semibold text-[#04276B]/50 dark:text-[#f3f4f6] uppercase tracking-[0.1em] first:pt-0">
        {{ $section['label'] }}
    </p>

    @foreach($section['links'] as $link)
        @php $active = request()->routeIs($link['match']); @endphp
        <a href="{{ route($link['route']) }}"
           class="sidebar-link group flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium transition-colors
                  {{ $active
                      ? ($esBachillerato
                          ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/20'
                          : 'bg-[#0606F0] text-white shadow-sm shadow-[#0606F0]/20')
                      : 'text-[#121D30]/70 dark:text-white/70 hover:text-[#0606F0] dark:hover:text-white hover:bg-[#04276B]/5 dark:hover:bg-white/10' }}">
            <svg class="w-[18px] h-[18px] flex-shrink-0 {{ $active ? 'text-white' : 'text-[#04276B]/40 dark:text-white/50 group-hover:text-[#0606F0] dark:group-hover:text-white' }}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/>
            </svg>
            <span>{{ $link['label'] }}</span>
        </a>
    @endforeach
@endforeach
