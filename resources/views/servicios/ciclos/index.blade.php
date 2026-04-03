<x-panel title="Ciclos Escolares" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="flex justify-end mb-6">
        <a href="{{ route('servicios.ciclos.create') }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            + Nuevo ciclo
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 flex flex-col min-h-0" style="max-height: calc(100vh - 220px);">
        <div class="overflow-y-auto flex-1 custom-scrollbar">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-center">Fecha inicio</th>
                    <th class="px-4 py-3 text-center">Fecha fin</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($ciclos as $ciclo)
                    @php $activo = now()->between($ciclo->fecha_inicio, $ciclo->fecha_fin); @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-bold text-gray-900 dark:text-gray-100">{{ $ciclo->nombre }}</td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $ciclo->fecha_inicio->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $ciclo->fecha_fin->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($activo)
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Activo</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('servicios.ciclos.edit', $ciclo) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 font-medium">Editar</a>
                                <form method="POST" action="{{ route('servicios.ciclos.destroy', $ciclo) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 font-medium"
                                            onclick="return confirm('¿Eliminar ciclo?')">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400 dark:text-gray-400">No hay ciclos escolares.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</x-panel>
