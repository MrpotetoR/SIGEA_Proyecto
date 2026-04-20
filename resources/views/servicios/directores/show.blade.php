<x-panel title="Detalle Director" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    @php $director->loadMissing('documentos', 'carrerasDirigidas'); @endphp
    <div class="max-w-5xl space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('servicios.directores.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline">← Volver</a>
            <a href="{{ route('servicios.directores.edit', $director) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Editar
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Datos generales --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $director->nombre_completo }}</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Correo</dt>
                        <dd class="font-medium dark:text-gray-200 break-all">{{ $director->user?->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Especialidad</dt>
                        <dd class="font-medium dark:text-gray-200">{{ $director->especialidad ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Número de cédula profesional</dt>
                        <dd class="font-medium dark:text-gray-200">{{ $director->num_cedula ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">RFC</dt>
                        <dd class="font-medium dark:text-gray-200 uppercase">{{ $director->rfc ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Estado</dt>
                        <dd>
                            @if($director->user?->activo)
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Activo</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Inactivo</span>
                            @endif
                        </dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-gray-500 dark:text-gray-400 mb-1">Carrera(s) que dirige</dt>
                        <dd>
                            @if($director->carrerasDirigidas->isNotEmpty())
                                @foreach($director->carrerasDirigidas as $c)
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 mb-1">{{ $c->nombre_carrera }}</span>
                                @endforeach
                            @else
                                <span class="text-gray-400 dark:text-gray-400">Sin asignar</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Documentación --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 flex flex-col" style="max-height: calc(100vh - 180px);">
                @php $docsExistentes = $director->documentos->keyBy('tipo'); @endphp
                <div class="mb-4 flex-shrink-0">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Documentación del director</h3>
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
    </div>
</x-panel>
