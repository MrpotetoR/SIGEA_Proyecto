<x-panel title="Grupos" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">Grupos de <span class="font-semibold text-gray-700">{{ $carrera?->nombre_carrera ?? 'Sin carrera' }}</span></p>
        <a href="{{ route('director.grupos.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Grupo
        </a>
    </div>

    @if($grupos->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <p class="text-gray-500 text-sm">No hay grupos registrados.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($grupos as $grupo)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-lg font-bold text-gray-900">{{ $grupo->clave_grupo }}</span>
                        <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 text-xs font-medium rounded-lg">{{ $grupo->cuatrimestre }}o Cuat.</span>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Ciclo: <span class="text-gray-700">{{ $grupo->cicloEscolar?->nombre ?? 'N/A' }}</span></p>
                    <p class="text-sm text-gray-500 mb-4">Tutor: <span class="text-gray-700">{{ $grupo->tutorDocente?->nombre_completo ?? 'Sin tutor' }}</span></p>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('director.grupos.show', $grupo->id_grupo) }}" class="flex-1 text-center px-3 py-1.5 bg-gray-50 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-100 transition-colors">Ver</a>
                        <a href="{{ route('director.grupos.edit', $grupo->id_grupo) }}" class="flex-1 text-center px-3 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-medium rounded-lg hover:bg-indigo-100 transition-colors">Editar</a>
                        <form method="POST" action="{{ route('director.grupos.destroy', $grupo->id_grupo) }}" onsubmit="return confirm('Eliminar este grupo?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 text-xs font-medium rounded-lg hover:bg-red-100 transition-colors">Eliminar</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-panel>
