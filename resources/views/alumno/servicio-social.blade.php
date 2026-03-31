<x-panel title="Servicio Social" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    @if(!$servicio)
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400 dark:bg-gray-800 dark:shadow-gray-900/20 dark:text-gray-600">
            Sin registro de servicio social. Acude a Servicios Escolares para más información.
        </div>
    @else
        <div class="space-y-6">

            {{-- Estado general --}}
            <div class="bg-white rounded-xl shadow p-6 dark:bg-gray-800 dark:shadow-gray-900/20">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200">Estado del Servicio Social</h3>
                    @php
                        $colorEstatus = match($servicio->estatus) {
                            'completado' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                            'en_curso'   => 'bg-yellow-100 text-yellow-800 dark:bg-amber-900/30 dark:text-amber-300',
                            default      => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $colorEstatus }}">
                        {{ ucfirst(str_replace('_', ' ', $servicio->estatus)) }}
                    </span>
                </div>

                {{-- Barra de progreso --}}
                @php
                    $requeridas = $servicio->horas_requeridas > 0 ? $servicio->horas_requeridas : 480;
                    $pct = min(100, round(($servicio->horas_acumuladas / $requeridas) * 100, 1));
                @endphp
                <div class="mb-2 flex justify-between text-sm text-gray-600 dark:text-gray-300">
                    <span>Horas acumuladas</span>
                    <span class="font-semibold">{{ $servicio->horas_acumuladas }} / {{ $requeridas }} hrs</span>
                </div>
                <div class="rainbow-track h-4 rainbow-glow">
                    <div class="rainbow-bar"
                         style="width: {{ $pct }}%"></div>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ $pct }}% completado</p>
            </div>

        </div>
    @endif

</x-panel>
