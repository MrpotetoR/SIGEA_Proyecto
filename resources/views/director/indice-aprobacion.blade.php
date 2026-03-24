<x-panel title="Indice de Aprobacion" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    <p class="text-sm text-gray-500 mb-6">Estadisticas de aprobacion y reprobacion — <span class="font-semibold text-gray-700">{{ $carrera?->nombre_carrera ?? 'Sin carrera' }}</span></p>

    @if($ciclo)
        <div class="bg-indigo-50 border border-indigo-100 rounded-2xl px-5 py-3 mb-6">
            <p class="text-sm text-indigo-700">Ciclo actual: <span class="font-semibold">{{ $ciclo->nombre }}</span></p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Aprobacion --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-[15px] font-semibold text-gray-800">Indice de Aprobacion</h3>
            </div>

            @if(!empty($aprobacion))
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total calificaciones</span>
                        <span class="text-lg font-bold text-gray-800">{{ $aprobacion['total'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Aprobados</span>
                        <span class="text-lg font-bold text-green-600">{{ $aprobacion['aprobados'] ?? 0 }}</span>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Porcentaje</span>
                            <span class="text-2xl font-bold text-green-600">{{ $aprobacion['porcentaje_aprobacion'] ?? 0 }}%</span>
                        </div>
                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 rounded-full transition-all duration-500" style="width: {{ $aprobacion['porcentaje_aprobacion'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500">No hay datos de aprobacion disponibles.</p>
            @endif
        </div>

        {{-- Reprobacion --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-[15px] font-semibold text-gray-800">Indice de Reprobacion</h3>
            </div>

            @if(!empty($reprobacion))
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total calificaciones</span>
                        <span class="text-lg font-bold text-gray-800">{{ $reprobacion['total'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Reprobados</span>
                        <span class="text-lg font-bold text-red-500">{{ $reprobacion['reprobados'] ?? 0 }}</span>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Porcentaje</span>
                            <span class="text-2xl font-bold text-red-500">{{ $reprobacion['porcentaje_reprobacion'] ?? 0 }}%</span>
                        </div>
                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-red-500 rounded-full transition-all duration-500" style="width: {{ $reprobacion['porcentaje_reprobacion'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500">No hay datos de reprobacion disponibles.</p>
            @endif
        </div>
    </div>
</x-panel>
