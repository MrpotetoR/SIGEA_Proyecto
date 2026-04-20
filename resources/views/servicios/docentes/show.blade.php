<x-panel title="Detalle Docente" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    @php $docente->loadMissing('documentos', 'carrerasImparte'); @endphp
    <div class="max-w-5xl space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('servicios.docentes.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline">← Volver</a>
            <a href="{{ route('servicios.docentes.edit', $docente) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Editar
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Datos generales --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $docente->nombre_completo }}</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500 dark:text-gray-400">Correo</dt><dd class="font-medium dark:text-gray-200 break-all">{{ $docente->user?->email }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Especialidad</dt><dd class="font-medium dark:text-gray-200">{{ $docente->especialidad ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Número de cédula profesional</dt><dd class="font-medium dark:text-gray-200">{{ $docente->num_cedula ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">RFC</dt><dd class="font-medium dark:text-gray-200 uppercase">{{ $docente->rfc ?? '—' }}</dd></div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Contrato</dt>
                        <dd class="font-medium dark:text-gray-200">
                            @if(is_null($docente->horas_contrato))
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Docente de Planta</span>
                            @else
                                {{ $docente->horas_contrato }} horas semanales
                            @endif
                        </dd>
                    </div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Es tutor</dt><dd class="font-medium dark:text-gray-200">{{ $docente->es_tutor ? 'Sí' : 'No' }}</dd></div>
                    <div class="col-span-2">
                        <dt class="text-gray-500 dark:text-gray-400 mb-1">Carreras que imparte</dt>
                        <dd>
                            @if($docente->carrerasImparte->isNotEmpty())
                                @foreach($docente->carrerasImparte as $c)
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 mb-1">{{ $c->nombre_carrera }}</span>
                                @endforeach
                            @else
                                <span class="text-gray-400">Sin carreras asignadas</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Documentación --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 flex flex-col" style="max-height: calc(100vh - 180px);">
                @php $docsExistentes = $docente->documentos->keyBy('tipo'); @endphp
                <div class="mb-4 flex-shrink-0">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Documentación del docente</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $docsExistentes->count() }}/{{ count(\App\Models\DocumentoDocente::TIPOS) }} documentos cargados
                    </p>
                </div>
                <div class="overflow-y-auto flex-1 pr-1 space-y-2">
                    @foreach(\App\Models\DocumentoDocente::TIPOS as $key => $label)
                        @php $doc = $docsExistentes->get($key); @endphp
                        <div class="border dark:border-gray-600 rounded-lg p-3 bg-gray-50 dark:bg-gray-700/50 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <x-icon name="document" class="w-4 h-4 text-gray-500 dark:text-gray-400 flex-shrink-0" />
                                <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $label }}</span>
                            </div>
                            @if($doc)
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <a href="{{ Storage::disk('public')->url($doc->archivo_path) }}" target="_blank"
                                       class="text-xs text-[#0606F0] dark:text-blue-400 hover:underline font-medium">Ver</a>
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <a href="{{ Storage::disk('public')->url($doc->archivo_path) }}" download
                                       class="text-xs text-green-700 dark:text-green-400 hover:underline font-medium">Descargar</a>
                                </div>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 flex-shrink-0">Faltante</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($docente->horarios->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-3">Horarios asignados</h3>
                <div class="space-y-2 text-sm">
                    @foreach($docente->horarios as $h)
                        <div class="flex justify-between border-b dark:border-gray-700 pb-2">
                            <span class="font-medium dark:text-gray-200">{{ $h->materia?->nombre_materia }}</span>
                            <span class="text-gray-500 dark:text-gray-400">{{ $h->grupo?->clave_grupo }} — {{ ucfirst($h->dia_semana) }} {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }}-{{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-panel>
