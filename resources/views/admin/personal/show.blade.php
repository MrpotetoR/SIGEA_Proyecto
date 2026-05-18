<x-panel title="Personal SE — {{ $personal->nombre_completo }}" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    <div class="max-w-5xl space-y-6">
        <a href="{{ route('admin.personal.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline inline-block">← Volver al listado</a>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $personal->nombre_completo }}</h2>
                        @if($personal->puede_asignar_carreras)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wide bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-700"
                                  title="Tiene permiso especial para asignar carreras a otros gestores">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                Permiso especial
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $personal->user?->email }}</p>
                    <p class="text-xs text-gray-400 mt-2">Especialidad: <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $personal->especialidad }}</span></p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.personal.edit', $personal) }}"
                       class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium px-4 py-2 rounded-lg">Editar</a>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                <div>
                    <p class="text-xs uppercase text-gray-400 mb-1">Cédula profesional</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $personal->num_cedula ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-400 mb-1">RFC</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $personal->rfc ?? '—' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Carreras asignadas</h3>
            @if($personal->carreras->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach($personal->carreras as $c)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                            {{ $c->nombre_carrera }} <span class="ml-2 text-blue-500">·</span> <span class="ml-2 text-blue-600">{{ $c->clave_carrera }}</span>
                        </span>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-3">{{ $personal->carreras->count() }} de {{ \App\Models\GestorEscolar::MAX_CARRERAS }} carreras asignadas.</p>
            @else
                <p class="text-sm text-gray-400">Sin carreras asignadas. <a href="{{ route('admin.asignaciones.index') }}" class="text-[#0606F0] hover:underline">Asignar ahora →</a></p>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Documentación</h3>
            @php $existentes = $personal->documentos->keyBy('tipo'); @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach(\App\Models\DocumentoPersonalSE::TIPOS as $key => $label)
                    @php $doc = $existentes[$key] ?? null; @endphp
                    <div class="border dark:border-gray-600 rounded-lg p-3 bg-gray-50 dark:bg-gray-700/50 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $label }}</p>
                        </div>
                        @if($doc)
                            <a href="{{ asset('storage/'.$doc->archivo_path) }}" target="_blank"
                               class="text-xs text-[#0606F0] dark:text-blue-400 hover:underline flex-shrink-0">Ver PDF →</a>
                        @else
                            <span class="text-xs text-amber-600 dark:text-amber-400 flex-shrink-0">Faltante</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-panel>
