@php
$sections = [
    [
        'label' => 'Principal',
        'links' => [
            ['route' => 'docente.dashboard', 'label' => 'Overview',     'match' => 'docente.dashboard',
             'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['route' => 'docente.perfil',    'label' => 'Mi Perfil',    'match' => 'docente.perfil',
             'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ]
    ],
    [
        'label' => 'Academico',
        'links' => [
            ['route' => 'docente.grupos',          'label' => 'Mis Grupos',      'match' => 'docente.grupos',
             'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['route' => 'docente.horario',         'label' => 'Mi Horario',      'match' => 'docente.horario',
             'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['route' => 'docente.asistencia',      'label' => 'Asistencia',      'match' => 'docente.asistencia*',
             'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ['route' => 'docente.calificaciones',  'label' => 'Calificaciones',  'match' => 'docente.calificaciones*',
             'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ]
    ],
    [
        'label' => 'Reportes',
        'links' => [
            ['route' => 'docente.reporte-asistencia',  'label' => 'Rep. Asistencia',  'match' => 'docente.reporte-asistencia',
             'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['route' => 'docente.reporte-rendimiento', 'label' => 'Rep. Rendimiento', 'match' => 'docente.reporte-rendimiento',
             'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        ]
    ],
    [
        'label' => 'Gestion',
        'links' => [
            ['route' => 'docente.horas-culturales.index', 'label' => 'Horas ACUDE',       'match' => 'docente.horas-culturales.*',
             'icon' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['route' => 'docente.servicio-social.index',  'label' => 'Servicio Social',   'match' => 'docente.servicio-social.*',
             'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
        ]
    ],
    [
        'label' => 'Informacion',
        'links' => [
            ['route' => 'docente.evaluacion-resultados', 'label' => 'Mi Evaluacion', 'match' => 'docente.evaluacion-resultados',
             'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
            ['route' => 'docente.noticias',              'label' => 'Noticias',      'match' => 'docente.noticias',
             'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
        ]
    ],
];
@endphp

@foreach($sections as $section)
    <p class="px-3 pt-4 pb-1.5 text-[10px] font-semibold text-gray-400 uppercase tracking-[0.1em] first:pt-0">
        {{ $section['label'] }}
    </p>

    @foreach($section['links'] as $link)
        @php $active = request()->routeIs($link['match']); @endphp
        <a href="{{ route($link['route']) }}"
           class="sidebar-link {{ $active ? 'active' : '' }} group flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium transition-colors
                  {{ $active
                      ? 'bg-gray-900 dark:bg-indigo-600 text-white shadow-sm'
                      : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
            <svg class="w-[18px] h-[18px] flex-shrink-0 {{ $active ? 'text-white' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/>
            </svg>
            <span>{{ $link['label'] }}</span>
        </a>
    @endforeach
@endforeach
