<x-panel title="Horarios" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">Horarios de <span class="font-semibold text-gray-700">{{ $carrera?->nombre_carrera ?? 'Sin carrera' }}</span></p>
        <a href="{{ route('director.horarios.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nuevo Horario
        </a>
    </div>

    @if($horarios->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <p class="text-gray-500 text-sm">No hay horarios registrados.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Grupo</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Materia</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Docente</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Dia</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Horario</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($horarios as $h)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg">{{ $h->grupo?->clave_grupo ?? 'N/A' }}</span>
                            </td>
                            <td class="px-5 py-3 text-gray-700">{{ $h->materia?->nombre_materia ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $h->docente?->nombre_completo ?? 'Sin docente' }}</td>
                            <td class="px-5 py-3 text-center capitalize text-gray-600">{{ $h->dia_semana }}</td>
                            <td class="px-5 py-3 text-center text-gray-600">{{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}</td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('director.horarios.edit', $h->id_horario) }}" class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs rounded-lg hover:bg-indigo-100 transition-colors">Editar</a>
                                    <form method="POST" action="{{ route('director.horarios.destroy', $h->id_horario) }}" onsubmit="return confirm('Eliminar este horario?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-red-50 text-red-600 text-xs rounded-lg hover:bg-red-100 transition-colors">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-panel>
