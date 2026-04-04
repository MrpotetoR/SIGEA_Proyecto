<x-panel title="Mis Tutorados" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <div class="flex items-center justify-between">
        <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Mis Tutorados</h1>
        <span class="text-[12px] font-medium text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-full">{{ $tutorados->count() }} alumno{{ $tutorados->count() !== 1 ? 's' : '' }}</span>
    </div>

    @if($tutorados->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($tutorados as $alumno)
                <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-[#0606F0] text-white flex items-center justify-center text-[11px] font-bold uppercase">
                                {{ substr($alumno->nombre, 0, 1) }}{{ substr($alumno->apellidos, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-[14px] font-bold text-gray-800 dark:text-gray-200">{{ $alumno->nombre_completo }}</p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ $alumno->matricula }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between text-[12px]">
                                <span class="text-gray-400 dark:text-gray-500">Carrera</span>
                                <span class="font-medium text-gray-700 dark:text-gray-300 text-right max-w-[60%]">{{ $alumno->carrera?->nombre_carrera ?? 'Sin carrera' }}</span>
                            </div>
                            <div class="flex justify-between text-[12px]">
                                <span class="text-gray-400 dark:text-gray-500">Cuatrimestre</span>
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $alumno->cuatrimestre_actual }}°</span>
                            </div>
                            <div class="flex justify-between text-[12px]">
                                <span class="text-gray-400 dark:text-gray-500">Estatus</span>
                                @php
                                    $estatusColors = [
                                        'activo' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                        'baja_temporal' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                        'baja_definitiva' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    ];
                                    $color = $estatusColors[$alumno->estatus] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                                @endphp
                                <span class="text-[11px] font-medium px-2 py-0.5 rounded-full {{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $alumno->estatus)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <svg class="mx-auto w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <p class="text-[14px] text-gray-400 dark:text-gray-500">No tienes tutorados asignados actualmente.</p>
        </div>
    @endif

</div>

</x-panel>
