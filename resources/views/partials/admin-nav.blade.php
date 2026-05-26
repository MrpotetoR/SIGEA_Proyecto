@php
$sections = [
    [
        'label' => 'Principal',
        'links' => [
            ['route' => 'admin.dashboard', 'label' => 'Overview', 'match' => 'admin.dashboard',
             'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ]
    ],
    [
        'label' => 'Personal',
        'links' => [
            ['route' => 'admin.personal.index', 'label' => 'Gestores Escolares', 'match' => 'admin.personal.*',
             'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
            ['route' => 'admin.asignaciones.index', 'label' => 'Asignación de carreras', 'match' => 'admin.asignaciones.*',
             'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
        ]
    ],
    [
        'label' => 'Administradores',
        'links' => [
            ['route' => 'admin.administradores.index', 'label' => 'Admins', 'match' => 'admin.administradores.*',
             'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
        ]
    ],
    [
        'label' => 'Caja General',
        'links' => [
            ['route' => 'admin.caja-general.index', 'label' => 'Dashboard', 'match' => 'admin.caja-general.index',
             'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ['route' => 'admin.caja-general.cobro-tramite.index', 'label' => 'Trámites', 'match' => 'admin.caja-general.cobro-tramite.*',
             'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['route' => 'admin.caja-general.consolidado', 'label' => 'Consolidado', 'match' => 'admin.caja-general.consolidado',
             'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        ]
    ],
    [
        'label' => 'Caja Chica',
        'links' => [
            ['route' => 'admin.caja-chica.fondo.edit', 'label' => 'Fondo', 'match' => 'admin.caja-chica.fondo.*',
             'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
            ['route' => 'admin.caja-chica.historial', 'label' => 'Historial', 'match' => 'admin.caja-chica.historial',
             'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ['route' => 'admin.caja-chica.correos.index', 'label' => 'Correos', 'match' => 'admin.caja-chica.correos.*',
             'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ]
    ],
    [
        'label' => 'Reportes',
        'links' => [
            ['route' => 'admin.reportes-consolidados', 'label' => 'Consolidados', 'match' => 'admin.reportes-consolidados',
             'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ]
    ],
    [
        'label' => 'Configuración',
        'links' => [
            ['route' => 'admin.configuracion.tienda', 'label' => 'Tienda', 'match' => 'admin.configuracion.tienda*',
             'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z'],
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
           class="sidebar-link group flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium transition-colors
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
