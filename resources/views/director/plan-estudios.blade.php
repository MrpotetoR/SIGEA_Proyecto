<x-panel title="Plan de Estudios" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    <p class="text-sm text-gray-500 mb-6">Malla curricular de <span class="font-semibold text-gray-700">{{ $carrera?->nombre_carrera ?? 'Sin carrera' }}</span></p>

    @if($materias->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <p class="text-gray-500 text-sm">No hay materias registradas en el plan de estudios.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($materias as $cuatrimestre => $materiasCuat)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-5 py-3">
                        <h3 class="text-sm font-semibold text-white">{{ $cuatrimestre }}o Cuatrimestre</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-0 divide-x divide-y divide-gray-100">
                        @foreach($materiasCuat as $materia)
                            <div class="p-4 hover:bg-gray-50/50 transition-colors">
                                <p class="text-sm font-medium text-gray-800">{{ $materia->nombre_materia }}</p>
                                <div class="flex items-center gap-3 mt-1.5">
                                    <span class="text-xs text-gray-400">{{ $materia->horas_semana ?? 0 }} hrs/semana</span>
                                    @if($materia->clave_materia ?? false)
                                        <span class="text-xs text-gray-400 font-mono">{{ $materia->clave_materia }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-panel>
