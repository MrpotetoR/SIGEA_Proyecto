<x-panel title="Mis Docentes" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    @if($docentes->isEmpty())
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400">
            Sin docentes asignados en el ciclo actual.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($docentes as $docente)
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-2xl flex-shrink-0">
                            👨‍🏫
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $docente->nombre }} {{ $docente->apellidos }}</p>
                            <p class="text-xs text-gray-500">{{ $docente->especialidad ?? 'Docente' }}</p>
                        </div>
                    </div>

                    {{-- Materias que imparte al alumno --}}
                    @if($docente->horarios->isNotEmpty())
                        <div class="space-y-2">
                            @foreach($docente->horarios->unique('id_materia') as $horario)
                                <div class="flex items-center gap-2 text-sm text-gray-700">
                                    <span class="w-2 h-2 rounded-full bg-indigo-400 flex-shrink-0"></span>
                                    {{ $horario->materia?->nombre_materia ?? '—' }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</x-panel>
