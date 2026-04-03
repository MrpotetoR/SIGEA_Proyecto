<x-panel title="Mi Horario" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Mi Horario</h1>

    @php
        $diasOrden = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $colores = [
            'lunes' => 'bg-sky-100 border-sky-200 text-sky-700',
            'martes' => 'bg-emerald-100 border-emerald-200 text-emerald-700',
            'miercoles' => 'bg-amber-100 border-amber-200 text-amber-700',
            'jueves' => 'bg-sky-100 border-sky-200 text-sky-700',
            'viernes' => 'bg-rose-100 border-rose-200 text-rose-700',
            'sabado' => 'bg-gray-100 border-gray-200 text-gray-700',
        ];
    @endphp

    <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="grid grid-cols-6 divide-x divide-gray-100 dark:divide-gray-700">
            @foreach($diasOrden as $dia)
                <div class="min-w-0">
                    <div class="px-3 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 text-center">
                        <p class="text-[12px] font-bold text-gray-700 dark:text-gray-300 capitalize">{{ $dia }}</p>
                    </div>
                    <div class="p-2 space-y-2 min-h-[200px]">
                        @foreach($horario[$dia] ?? [] as $h)
                            @php $color = $colores[$dia] ?? 'bg-gray-100 text-gray-700'; @endphp
                            <div class="p-2.5 rounded-xl {{ $color }} border text-center">
                                <p class="text-[11px] font-bold truncate">{{ $h->materia?->nombre_materia ?? 'Materia' }}</p>
                                <p class="text-[10px] mt-0.5 opacity-80">{{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}</p>
                                <p class="text-[9px] mt-0.5 opacity-70 truncate">{{ $h->grupo?->clave_grupo ?? '' }}</p>
                            </div>
                        @endforeach
                        @if(empty($horario[$dia] ?? []))
                            <p class="text-[11px] text-gray-300 dark:text-gray-600 text-center pt-8">Sin clases</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

</x-panel>
