<x-panel title="Docentes" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    @if($carrera)
        <div class="mb-5">
            <p class="text-sm text-gray-500">Docentes asignados a <span class="font-semibold text-gray-700">{{ $carrera->nombre_carrera }}</span></p>
        </div>
    @endif

    @if($docentes->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
            </svg>
            <p class="text-gray-500 text-sm">No hay docentes registrados en esta carrera.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Docente</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Especialidad</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Materias</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Grupos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($docentes as $doc)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-violet-100 flex items-center justify-center text-violet-700 text-xs font-bold">
                                        {{ strtoupper(substr($doc->nombre ?? 'D', 0, 1)) }}{{ strtoupper(substr($doc->apellidos ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $doc->nombre_completo }}</p>
                                        <p class="text-xs text-gray-400">{{ $doc->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-gray-600">{{ $doc->especialidad ?? 'N/A' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($doc->horarios->pluck('materia.nombre_materia')->unique()->filter() as $materia)
                                        <span class="inline-block px-2 py-0.5 bg-blue-50 text-blue-700 text-xs rounded-lg">{{ $materia }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($doc->horarios->pluck('grupo.clave_grupo')->unique()->filter() as $grupo)
                                        <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-lg">{{ $grupo }}</span>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-panel>
