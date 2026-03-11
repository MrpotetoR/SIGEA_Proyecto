<x-panel title="Detalle de Ciclo Escolar" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-2xl space-y-6">
        <a href="{{ route('servicios.ciclos.index') }}" class="text-sm text-indigo-600 hover:underline inline-block">← Volver</a>

        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $ciclo->nombre }}</h2>
                    @php
                        $hoy = now();
                        $activo = $hoy->between($ciclo->fecha_inicio, $ciclo->fecha_fin);
                        $futuro = $hoy->lt($ciclo->fecha_inicio);
                    @endphp
                    <span class="inline-flex items-center mt-2 px-3 py-1 rounded-full text-xs font-semibold
                        {{ $activo ? 'bg-green-100 text-green-800' : ($futuro ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-500') }}">
                        {{ $activo ? 'En curso' : ($futuro ? 'Próximo' : 'Finalizado') }}
                    </span>
                </div>
                <a href="{{ route('servicios.ciclos.edit', $ciclo) }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Editar
                </a>
            </div>

            <dl class="grid grid-cols-2 gap-x-8 gap-y-4 text-sm">
                <div>
                    <dt class="text-gray-400 text-xs uppercase tracking-wide">Fecha de inicio</dt>
                    <dd class="mt-1 font-medium text-gray-800">{{ $ciclo->fecha_inicio->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs uppercase tracking-wide">Fecha de fin</dt>
                    <dd class="mt-1 font-medium text-gray-800">{{ $ciclo->fecha_fin->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs uppercase tracking-wide">Duración</dt>
                    <dd class="mt-1 font-medium text-gray-800">
                        {{ $ciclo->fecha_inicio->diffInWeeks($ciclo->fecha_fin) }} semanas
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs uppercase tracking-wide">ID Ciclo</dt>
                    <dd class="mt-1 font-mono text-xs text-gray-500">{{ $ciclo->id_ciclo }}</dd>
                </div>
            </dl>

            <div class="flex gap-3 pt-6 mt-6 border-t">
                <a href="{{ route('servicios.ciclos.edit', $ciclo) }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                    Editar ciclo
                </a>
                <form method="POST" action="{{ route('servicios.ciclos.destroy', $ciclo) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium px-5 py-2"
                            onclick="return confirm('¿Eliminar este ciclo escolar?')">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-panel>
