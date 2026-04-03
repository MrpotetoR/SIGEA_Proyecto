@php
$sections = [
    [
        'label' => 'Principal',
        'links' => [
            ['route' => 'alumno.dashboard', 'label' => 'Overview',       'match' => 'alumno.dashboard',
             'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['route' => 'alumno.perfil',    'label' => 'Mi Perfil',      'match' => 'alumno.perfil',
             'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            ['route' => 'alumno.horario',   'label' => 'Horario',        'match' => 'alumno.horario',
             'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ]
    ],
    [
        'label' => 'Académico',
        'links' => [
            ['route' => 'alumno.calificaciones', 'label' => 'Calificaciones', 'match' => 'alumno.calificaciones',
             'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['route' => 'alumno.kardex',         'label' => 'Kardex',         'match' => 'alumno.kardex*',
             'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['route' => 'alumno.historial',      'label' => 'Historial',      'match' => 'alumno.historial',
             'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ]
    ],
    [
        'label' => 'Actividades',
        'links' => [
            ['route' => 'alumno.horas-culturales',   'label' => 'Horas ACUDE',       'match' => 'alumno.horas-culturales',
             'icon' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['route' => 'alumno.servicio-social',    'label' => 'Servicio Social',   'match' => 'alumno.servicio-social',
             'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['route' => 'alumno.evaluacion-docente', 'label' => 'Evaluar Docentes',  'match' => 'alumno.evaluacion-docente',
             'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
        ]
    ],
    [
        'label' => 'Información',
        'links' => [
            ['route' => 'alumno.mis-docentes', 'label' => 'Mis Docentes', 'match' => 'alumno.mis-docentes',
             'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['route' => 'alumno.noticias',     'label' => 'Noticias',     'match' => 'alumno.noticias',
             'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
        ]
    ],
];
@endphp

@foreach($sections as $section)
    <p class="px-3 pt-4 pb-1.5 text-[10px] font-semibold text-[#04276B]/50 dark:text-[#E5CCBE] uppercase tracking-[0.1em] first:pt-0">
        {{ $section['label'] }}
    </p>

    @foreach($section['links'] as $link)
        @php $active = request()->routeIs($link['match']); @endphp
        <a href="{{ route($link['route']) }}"
           class="sidebar-link {{ $active ? 'active' : '' }} group flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium transition-colors
                  {{ $active
                      ? 'bg-[#0606F0] text-white shadow-sm shadow-[#0606F0]/20'
                      : 'text-[#121D30]/70 dark:text-white/70 hover:text-[#0606F0] dark:hover:text-white hover:bg-[#04276B]/5 dark:hover:bg-white/10' }}">
            <svg class="w-[18px] h-[18px] flex-shrink-0 {{ $active ? 'text-white' : 'text-[#04276B]/40 dark:text-white/50 group-hover:text-[#0606F0] dark:group-hover:text-white' }}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/>
            </svg>
            <span>{{ $link['label'] }}</span>
        </a>
    @endforeach
@endforeach
