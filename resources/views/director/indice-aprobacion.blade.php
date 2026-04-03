<x-panel title="Indice de Aprobacion" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Estadisticas de aprobacion y reprobacion — <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $carrera?->nombre_carrera ?? 'Sin carrera' }}</span></p>

    @if($ciclo)
        <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-700 rounded-2xl px-5 py-3 mb-6">
            <p class="text-sm text-blue-700 dark:text-blue-400">Ciclo actual: <span class="font-semibold">{{ $ciclo->nombre }}</span></p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Aprobacion --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-green-50 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-[15px] font-semibold text-gray-800 dark:text-gray-200">Indice de Aprobacion</h3>
            </div>

            @if(!empty($aprobacion))
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total calificaciones</span>
                        <span class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $aprobacion['total'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Aprobados</span>
                        <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ $aprobacion['aprobadas'] ?? 0 }}</span>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Porcentaje</span>
                            <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $aprobacion['porcentaje_aprobacion'] ?? 0 }}%</span>
                        </div>
                        <div class="rainbow-track h-3 rainbow-glow">
                            <div class="rainbow-bar" style="width: {{ $aprobacion['porcentaje_aprobacion'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay datos de aprobacion disponibles.</p>
            @endif
        </div>

        {{-- Reprobacion --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-red-50 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-[15px] font-semibold text-gray-800 dark:text-gray-200">Indice de Reprobacion</h3>
            </div>

            @if(!empty($reprobacion))
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total calificaciones</span>
                        <span class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $reprobacion['total'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Reprobados</span>
                        <span class="text-lg font-bold text-red-500 dark:text-red-400">{{ $reprobacion['reprobadas'] ?? 0 }}</span>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Porcentaje</span>
                            <span class="text-2xl font-bold text-red-500 dark:text-red-400">{{ $reprobacion['porcentaje_reprobacion'] ?? 0 }}%</span>
                        </div>
                        <div class="rainbow-track h-3 rainbow-glow">
                            <div class="rainbow-bar" style="width: {{ $reprobacion['porcentaje_reprobacion'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay datos de reprobacion disponibles.</p>
            @endif
        </div>
    </div>
</x-panel>
