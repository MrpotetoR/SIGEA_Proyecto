<x-panel title="Evaluar Docentes" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    <p class="text-sm text-gray-500 mb-6">
        Evalúa a los docentes del ciclo actual. Cada docente puede evaluarse una sola vez por ciclo.
    </p>

    @if($docentes->isEmpty())
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400">
            Sin docentes disponibles para evaluar en el ciclo actual.
        </div>
    @else
        <div class="grid grid-cols-1 gap-6">
            @foreach($docentes as $docente)
                @php $yaEvaluado = $evaluados->contains($docente->id_docente); @endphp
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-6 py-4 flex items-center justify-between
                        {{ $yaEvaluado ? 'bg-green-50 border-b border-green-200' : 'bg-indigo-50 border-b border-indigo-200' }}">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $docente->nombre }} {{ $docente->apellidos }}</p>
                            <p class="text-xs text-gray-500">{{ $docente->especialidad ?? 'Docente' }}</p>
                        </div>
                        @if($yaEvaluado)
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                ✅ Evaluado
                            </span>
                        @endif
                    </div>

                    @if(!$yaEvaluado && $preguntas->isNotEmpty())
                        <form action="{{ route('alumno.evaluacion-docente.store') }}" method="POST" class="p-6 space-y-4">
                            @csrf
                            <input type="hidden" name="id_docente" value="{{ $docente->id_docente }}">

                            @foreach($preguntas as $pregunta)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $pregunta->texto_pregunta ?? $pregunta->pregunta ?? 'Pregunta ' . $loop->iteration }}
                                    </label>
                                    <div class="flex gap-4">
                                        @for($v = 1; $v <= 5; $v++)
                                            <label class="flex flex-col items-center gap-1 cursor-pointer">
                                                <input type="radio" name="respuestas[{{ $pregunta->id_pregunta }}]"
                                                       value="{{ $v }}" required
                                                       class="accent-indigo-600">
                                                <span class="text-xs text-gray-500">{{ $v }}</span>
                                            </label>
                                        @endfor
                                        <span class="text-xs text-gray-400 self-end ml-2">(1=Muy malo · 5=Excelente)</span>
                                    </div>
                                </div>
                            @endforeach

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Comentarios (opcional)</label>
                                <textarea name="comentarios" rows="2" maxlength="500"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                    placeholder="Escribe tus comentarios..."></textarea>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                    Enviar evaluación
                                </button>
                            </div>
                        </form>
                    @elseif(!$yaEvaluado)
                        <p class="px-6 py-4 text-sm text-gray-400">No hay preguntas configuradas para la encuesta.</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</x-panel>
