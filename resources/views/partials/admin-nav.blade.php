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
            ['route' => 'admin.personal.index', 'label' => 'Personal S.E.', 'match' => 'admin.personal.*',
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
