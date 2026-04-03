<x-panel title="Evaluacion Docente" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Resultados de evaluacion docente — <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $carrera?->nombre_carrera ?? 'Sin carrera' }}</span></p>

    @if($ciclo)
        <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-700 rounded-2xl px-5 py-3 mb-6">
            <p class="text-sm text-blue-700 dark:text-blue-400">Ciclo: <span class="font-semibold">{{ $ciclo->nombre }}</span></p>
        </div>
    @endif

    @if($promedios instanceof \Illuminate\Support\Collection && $promedios->isEmpty() || empty($promedios))
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 text-sm">No hay resultados de evaluaciones docentes disponibles.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($promedios as $eval)
                @php
                    $promedio = $eval['promedio'] ?? 0;
                    $nombre = $eval['docente']->nombre_completo ?? 'N/A';
                    $colorBarra = $promedio >= 9 ? 'bg-green-500' : ($promedio >= 7 ? 'bg-yellow-400' : 'bg-red-500');
                    $colorTexto = $promedio >= 9 ? 'text-green-600 dark:text-green-400' : ($promedio >= 7 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-500 dark:text-red-400');
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center text-sky-700 dark:text-sky-400 text-sm font-bold">
                            {{ strtoupper(substr($nombre, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $nombre }}</p>
                        </div>
                    </div>
                    <div class="flex items-end justify-between mb-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Promedio evaluacion</span>
                        <span class="text-xl font-bold {{ $colorTexto }}">{{ number_format($promedio, 1) }}</span>
                    </div>
                    <div class="rainbow-track h-2 rainbow-glow">
                        <div class="rainbow-bar" style="width: {{ ($promedio / 10) * 100 }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-panel>
