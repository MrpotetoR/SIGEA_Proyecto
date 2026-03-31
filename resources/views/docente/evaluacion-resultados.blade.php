<x-panel title="Mi Evaluacion" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <div class="flex items-center justify-between">
        <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Resultados de Evaluacion Docente</h1>
        @if($ciclos->isNotEmpty())
            <form method="GET" class="flex items-center gap-2">
                <select name="ciclo" onchange="this.form.submit()" class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2 text-[12px] focus:ring-2 focus:ring-violet-300 outline-none">
                    @foreach($ciclos as $c)
                        <option value="{{ $c->id_ciclo }}" {{ $ciclo?->id_ciclo == $c->id_ciclo ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    {{-- Promedio general --}}
    <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-orange-200">
                {{ number_format($promedio, 1) }}
            </div>
            <div>
                <p class="text-[16px] font-bold text-gray-900 dark:text-gray-100">Promedio General de Evaluacion</p>
                <p class="text-[12px] text-gray-400 dark:text-gray-500 mt-0.5">Basado en {{ $evaluaciones->count() }} evaluaciones del ciclo {{ $ciclo?->nombre ?? 'actual' }}</p>
            </div>
        </div>
    </div>

    @if($evaluaciones->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">#</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Calificacion</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Comentario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($evaluaciones as $i => $eval)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-3 text-[12px] text-gray-400 dark:text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 text-center">
                                @php $c = $eval->calificacion_promedio ?? 0; @endphp
                                <span class="inline-block px-2.5 py-1 rounded-lg text-[12px] font-bold {{ $c >= 8 ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300' : ($c >= 6 ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-red-50 text-red-500 dark:bg-red-900/30 dark:text-red-400') }}">
                                    {{ number_format($c, 1) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-[12px] text-gray-500 dark:text-gray-400">{{ $eval->comentarios ?? 'Sin comentario' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-[14px] text-gray-400 dark:text-gray-500">Sin evaluaciones en este ciclo.</p>
        </div>
    @endif

</div>

</x-panel>
