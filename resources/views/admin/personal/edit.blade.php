<x-panel title="Editar Personal SE — {{ $personal->nombre_completo }}" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    <div class="max-w-5xl">
        <a href="{{ route('admin.personal.show', $personal) }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-4 inline-block">← Volver</a>

        @if($errors->any())
            <div class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.personal.update', $personal) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-6">Datos del personal</h2>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre(s) *</label>
                                <input type="text" name="nombre" value="{{ old('nombre', $personal->nombre) }}" required maxlength="80"
                                       pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                                       oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Apellidos *</label>
                                <input type="text" name="apellidos" value="{{ old('apellidos', $personal->apellidos) }}" required maxlength="100"
                                       pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                                       oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correo electrónico *</label>
                            <input type="email" name="email" value="{{ old('email', $personal->user?->email) }}" required maxlength="255"
                                   class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cédula profesional</label>
                                <input type="text" name="num_cedula" value="{{ old('num_cedula', $personal->num_cedula) }}" maxlength="30"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">RFC</label>
                                <input type="text" name="rfc" value="{{ old('rfc', $personal->rfc) }}" maxlength="20"
                                       oninput="this.value = this.value.toUpperCase()"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm uppercase focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Especialidad *</label>
                            <input type="text" name="especialidad" value="{{ old('especialidad', $personal->especialidad) }}" required maxlength="150"
                                   class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg px-3 py-2">
                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                <strong>Carreras asignadas actualmente:</strong>
                                @if($personal->carreras->isNotEmpty())
                                    {{ $personal->carreras->pluck('nombre_carrera')->implode(', ') }}
                                @else
                                    Sin carreras asignadas.
                                @endif
                            </p>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                Para modificar las carreras, ve a
                                <a href="{{ route('admin.asignaciones.index') }}" class="font-semibold underline">Asignación de carreras</a>.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 flex flex-col" style="max-height: calc(100vh - 180px);">
                    <div class="mb-4 flex-shrink-0">
                        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Documentación</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sube un nuevo archivo solo si quieres reemplazar el existente.</p>
                    </div>

                    <div class="overflow-y-auto flex-1 custom-scrollbar pr-1 space-y-3">
                        @php $existentes = $personal->documentos->keyBy('tipo'); @endphp
                        @foreach(\App\Models\DocumentoPersonalSE::TIPOS as $key => $label)
                            @php $doc = $existentes[$key] ?? null; @endphp
                            <div class="border dark:border-gray-600 rounded-lg p-3 bg-gray-50 dark:bg-gray-700/50">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $label }}</label>
                                @if($doc)
                                    <a href="{{ asset('storage/'.$doc->archivo_path) }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs text-[#0606F0] dark:text-blue-400 hover:underline mb-2">
                                        Ver archivo actual ↗
                                    </a>
                                @else
                                    <p class="text-xs text-amber-600 dark:text-amber-400 mb-2">Sin archivo cargado.</p>
                                @endif
                                <input type="file" name="documentos[{{ $key }}]" accept="application/pdf"
                                       class="block w-full text-xs text-gray-600 dark:text-gray-300
                                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                              file:text-xs file:font-medium
                                              file:bg-[#0606F0] file:text-white
                                              hover:file:bg-[#04276B] cursor-pointer">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-4 border border-transparent dark:border-gray-700 flex flex-wrap gap-3">
                <button type="submit" class="bg-[#0606F0] hover:bg-[#04276B] text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">Guardar cambios</button>
                <a href="{{ route('admin.personal.show', $personal) }}" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
            </div>
        </form>
    </div>
</x-panel>
