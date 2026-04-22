<x-panel title="Mi Horario" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Mi Horario</h1>

    @php
        // Paleta por dia (coincide con el horario del alumno para mantener identidad visual)
        $colores = [
            'lunes'     => ['bg'=>'bg-sky-50 dark:bg-sky-900/30',        'border'=>'border-sky-200 dark:border-sky-700/50',        'title'=>'text-sky-800 dark:text-sky-200',        'sub'=>'text-sky-600 dark:text-sky-300'],
            'martes'    => ['bg'=>'bg-emerald-50 dark:bg-emerald-900/30','border'=>'border-emerald-200 dark:border-emerald-700/50','title'=>'text-emerald-800 dark:text-emerald-200','sub'=>'text-emerald-600 dark:text-emerald-300'],
            'miercoles' => ['bg'=>'bg-amber-50 dark:bg-amber-900/30',    'border'=>'border-amber-200 dark:border-amber-700/50',    'title'=>'text-amber-800 dark:text-amber-200',    'sub'=>'text-amber-600 dark:text-amber-300'],
            'jueves'    => ['bg'=>'bg-indigo-50 dark:bg-indigo-900/30',  'border'=>'border-indigo-200 dark:border-indigo-700/50',  'title'=>'text-indigo-800 dark:text-indigo-200',  'sub'=>'text-indigo-600 dark:text-indigo-300'],
            'viernes'   => ['bg'=>'bg-rose-50 dark:bg-rose-900/30',      'border'=>'border-rose-200 dark:border-rose-700/50',      'title'=>'text-rose-800 dark:text-rose-200',      'sub'=>'text-rose-600 dark:text-rose-300'],
            'sabado'    => ['bg'=>'bg-slate-50 dark:bg-slate-800/40',    'border'=>'border-slate-200 dark:border-slate-600/50',    'title'=>'text-slate-800 dark:text-slate-200',    'sub'=>'text-slate-600 dark:text-slate-300'],
        ];

        // Rango de horas derivado del horario real del docente (con fallback 07-15)
        $minH = 24; $maxH = 0;
        foreach ($dias as $d) {
            foreach ($horario[$d] ?? [] as $c) {
                $minH = min($minH, (int) \Carbon\Carbon::parse($c->hora_inicio)->format('H'));
                $maxH = max($maxH, (int) \Carbon\Carbon::parse($c->hora_fin)->format('H'));
            }
        }
        if ($minH === 24) { $minH = 7; $maxH = 15; }
        $horas = [];
        for ($h = $minH; $h < $maxH; $h++) { $horas[] = sprintf('%02d:00', $h); }

        // Mapa de ocupacion: [dia][HH:00] = $clase (fila de inicio) | 'SPAN' (horas intermedias)
        $ocupado = [];
        foreach ($dias as $d) {
            foreach ($horario[$d] ?? [] as $c) {
                $ini = (int) \Carbon\Carbon::parse($c->hora_inicio)->format('H');
                $fin = (int) \Carbon\Carbon::parse($c->hora_fin)->format('H');
                for ($h = $ini; $h < max($fin, $ini + 1); $h++) {
                    $key = sprintf('%02d:00', $h);
                    $ocupado[$d][$key] = $h === $ini ? $c : 'SPAN';
                }
            }
        }

        $hayClases = collect($horario)->flatten()->isNotEmpty();
    @endphp

    <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
        <table class="min-w-full table-fixed" style="min-width:640px">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                    <th class="w-20 px-3 py-3 text-center text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hora</th>
                    @foreach($dias as $dia)
                        <th class="px-3 py-3 text-center text-[12px] font-bold text-gray-700 dark:text-gray-300 capitalize">{{ $dia }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($horas as $hora)
                    <tr class="h-16">
                        <td class="px-3 py-2 text-center text-[12px] text-gray-500 dark:text-gray-400 font-mono whitespace-nowrap border-r border-gray-100 dark:border-gray-700">{{ $hora }}</td>
                        @foreach($dias as $dia)
                            @php $cell = $ocupado[$dia][$hora] ?? null; @endphp
                            @if($cell === 'SPAN')
                                {{-- Celda cubierta por el rowspan de la fila anterior --}}
                            @elseif($cell)
                                @php
                                    $ini = (int) \Carbon\Carbon::parse($cell->hora_inicio)->format('H');
                                    $fin = (int) \Carbon\Carbon::parse($cell->hora_fin)->format('H');
                                    $rowspan = max(1, $fin - $ini);
                                    $c = $colores[$dia] ?? $colores['sabado'];
                                @endphp
                                <td rowspan="{{ $rowspan }}" class="p-1.5 align-middle border-l border-gray-100 dark:border-gray-700">
                                    <div class="h-full flex flex-col justify-center p-2.5 rounded-xl border {{ $c['bg'] }} {{ $c['border'] }} text-center">
                                        <p class="text-[11px] font-bold {{ $c['title'] }} leading-tight">{{ $cell->materia?->nombre_materia ?? 'Materia' }}</p>
                                        <p class="text-[10px] mt-1 {{ $c['sub'] }}">{{ \Carbon\Carbon::parse($cell->hora_inicio)->format('H:i') }} – {{ \Carbon\Carbon::parse($cell->hora_fin)->format('H:i') }}</p>
                                        <p class="text-[9px] mt-0.5 {{ $c['sub'] }} opacity-80 truncate">{{ $cell->grupo?->clave_grupo ?? '' }}</p>
                                    </div>
                                </td>
                            @else
                                <td class="border-l border-gray-100 dark:border-gray-700"></td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach

                @unless($hayClases)
                    <tr>
                        <td colspan="{{ count($dias) + 1 }}" class="px-4 py-10 text-center text-[12px] text-gray-400 dark:text-gray-500">Sin clases registradas</td>
                    </tr>
                @endunless
            </tbody>
        </table>
        </div>
    </div>

</div>

</x-panel>
