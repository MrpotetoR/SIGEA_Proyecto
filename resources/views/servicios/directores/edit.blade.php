<x-panel title="Editar Director" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-5xl">
        <a href="{{ route('servicios.directores.show', $director) }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-4 inline-block">← Volver</a>

        @php
            $docsExistentes = $director->documentos->keyBy('tipo');
            $carreraActual = $director->carrerasDirigidas->first()?->id_carrera;
        @endphp

        <form method="POST" action="{{ route('servicios.directores.update', $director) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Contenedor 1: Datos del director --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-6">Datos del director</h2>

                    <div class="space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre(s) *</label>
                                <input type="text" name="nombre" value="{{ old('nombre', $director->nombre) }}" required maxlength="80"
                                       pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                                       title="Solo letras y espacios"
                                       oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('nombre') border-red-400 @enderror">
                                @error('nombre')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Apellidos *</label>
                                <input type="text" name="apellidos" value="{{ old('apellidos', $director->apellidos) }}" required maxlength="100"
                                       pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                                       title="Solo letras y espacios"
                                       oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('apellidos') border-red-400 @enderror">
                                @error('apellidos')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número de cédula profesional</label>
                                <input type="text" name="num_cedula" value="{{ old('num_cedula', $director->num_cedula) }}" maxlength="30"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('num_cedula') border-red-400 @enderror">
                                @error('num_cedula')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">RFC</label>
                                <input type="text" name="rfc" value="{{ old('rfc', $director->rfc) }}" maxlength="20"
                                       oninput="this.value = this.value.toUpperCase()"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm uppercase focus:ring-2 focus:ring-blue-400 focus:outline-none @error('rfc') border-red-400 @enderror">
                                @error('rfc')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Especialidad</label>
                                <input type="text" name="especialidad" value="{{ old('especialidad', $director->especialidad) }}" maxlength="100"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Carrera a dirigir</label>
                                <select name="id_carrera"
                                        class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                                    <option value="">Sin asignar</option>
                                    @foreach($carreras as $c)
                                        <option value="{{ $c->id_carrera }}" @selected(old('id_carrera', $carreraActual) == $c->id_carrera)>{{ $c->nombre_carrera }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contenedor 2: Documentación del director --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 flex flex-col" style="max-height: calc(100vh - 180px);">
                    <div class="flex items-start justify-between mb-4 flex-shrink-0">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Documentación del director</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Todos los documentos son <span class="font-semibold text-red-600 dark:text-red-400">obligatorios</span>. Sube un PDF nuevo para reemplazar el actual (máx. 5 MB).</p>
                        </div>
                    </div>

                    <div class="overflow-y-auto flex-1 custom-scrollbar pr-1 space-y-3">
                        @foreach(\App\Models\DocumentoDocente::TIPOS as $key => $label)
                            @php $doc = $docsExistentes->get($key); @endphp
                            <div class="border dark:border-gray-600 rounded-lg p-3 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-start justify-between mb-2 gap-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>
                                    @if($doc)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 flex-shrink-0">Cargado</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-200 text-gray-600 dark:bg-gray-600 dark:text-gray-300 flex-shrink-0">Pendiente</span>
                                    @endif
                                </div>

                                @if($doc)
                                    <div class="flex items-center gap-2 mb-2">
                                        <a href="{{ Storage::disk('public')->url($doc->archivo_path) }}" target="_blank"
                                           class="text-xs text-[#0606F0] dark:text-blue-400 hover:underline font-medium">Ver archivo actual</a>
                                        <span class="text-gray-300 dark:text-gray-600">|</span>
                                        <a href="{{ Storage::disk('public')->url($doc->archivo_path) }}" download
                                           class="text-xs text-green-700 dark:text-green-400 hover:underline font-medium">Descargar</a>
                                    </div>
                                @endif

                                <input type="file" name="documentos[{{ $key }}]" accept="application/pdf"
                                       class="block w-full text-xs text-gray-600 dark:text-gray-300
                                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                              file:text-xs file:font-medium
                                              file:bg-[#0606F0] file:text-white
                                              hover:file:bg-[#04276B] cursor-pointer
                                              @error('documentos.'.$key) ring-1 ring-red-400 @enderror">
                                <p class="text-[10px] text-gray-400 dark:text-gray-400 mt-1">
                                    {{ $doc ? 'Selecciona un archivo para reemplazar el actual.' : 'Selecciona un PDF para subir.' }}
                                </p>
                                @error('documentos.'.$key)
                                    <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-4 border border-transparent dark:border-gray-700 flex gap-3">
                <button type="submit"
                        class="bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    Guardar cambios
                </button>
                <a href="{{ route('servicios.directores.show', $director) }}"
                   class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Cancelar
                </a>
            </div>
        </form>

    </div>
</x-panel>
