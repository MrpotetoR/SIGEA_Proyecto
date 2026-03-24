<x-panel title="Alumnos" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    @if($carrera)
        <div class="mb-5">
            <p class="text-sm text-gray-500">Alumnos de <span class="font-semibold text-gray-700">{{ $carrera->nombre_carrera }}</span></p>
        </div>
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('director.alumnos') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <div class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1.5">Estatus</label>
                <select name="estatus" class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estatus') == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="baja" {{ request('estatus') == 'baja' ? 'selected' : '' }}>Baja</option>
                    <option value="egresado" {{ request('estatus') == 'egresado' ? 'selected' : '' }}>Egresado</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1.5">Cuatrimestre</label>
                <select name="cuatrimestre" class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                    <option value="">Todos</option>
                    @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ request('cuatrimestre') == $i ? 'selected' : '' }}>{{ $i }}o Cuatrimestre</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="px-5 py-2 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-colors">
                Filtrar
            </button>
        </div>
    </form>

    {{-- Tabla --}}
    @if($alumnos instanceof \Illuminate\Pagination\LengthAwarePaginator && $alumnos->isEmpty() || $alumnos instanceof \Illuminate\Support\Collection && $alumnos->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <p class="text-gray-500 text-sm">No se encontraron alumnos con los filtros seleccionados.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Matricula</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Alumno</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Cuatrimestre</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Estatus</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Semaforo</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($alumnos as $alumno)
                        @php
                            $semaforo = $alumno->semaforosAcademicos->last();
                            $nivel = $semaforo->nivel ?? 'sin datos';
                            $colorSemaforo = match($nivel) {
                                'verde' => 'bg-green-100 text-green-700',
                                'amarillo' => 'bg-yellow-100 text-yellow-700',
                                'rojo' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-500',
                            };
                            $colorEstatus = match($alumno->estatus) {
                                'activo' => 'bg-emerald-100 text-emerald-700',
                                'baja' => 'bg-red-100 text-red-700',
                                'egresado' => 'bg-blue-100 text-blue-700',
                                default => 'bg-gray-100 text-gray-500',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $alumno->matricula }}</td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">{{ $alumno->nombre_completo }}</p>
                            </td>
                            <td class="px-5 py-3 text-center text-gray-600">{{ $alumno->cuatrimestre_actual ?? '-' }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-lg capitalize {{ $colorEstatus }}">{{ $alumno->estatus }}</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-lg capitalize {{ $colorSemaforo }}">{{ $nivel }}</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <a href="{{ route('director.alumnos.historial', $alumno->id_alumno) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-medium rounded-lg hover:bg-indigo-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Historial
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($alumnos instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-5">
                {{ $alumnos->withQueryString()->links() }}
            </div>
        @endif
    @endif
</x-panel>
