<x-panel title="Editar Alumno" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-6xl">
        <a href="{{ route('servicios.alumnos.show', $alumno) }}"
           class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">← Volver al perfil</a>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-6">Editar datos — {{ $alumno->matricula }}</h2>

            <form method="POST" action="{{ route('servicios.alumnos.update', $alumno) }}" class="space-y-5" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre(s) *</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $alumno->nombre) }}" required maxlength="80"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                               title="Solo letras y espacios"
                               oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('nombre') border-red-400 @enderror">
                        @error('nombre')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Apellidos *</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos', $alumno->apellidos) }}" required maxlength="100"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                               title="Solo letras y espacios"
                               oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('apellidos') border-red-400 @enderror">
                        @error('apellidos')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Carrera *</label>
                        <select name="id_carrera" required
                                class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            @foreach($carreras as $c)
                                <option value="{{ $c->id_carrera }}" @selected(old('id_carrera', $alumno->id_carrera) == $c->id_carrera)>
                                    {{ $c->nombre_carrera }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cuatrimestre *</label>
                        <select name="cuatrimestre_actual" required
                                class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" @selected(old('cuatrimestre_actual', $alumno->cuatrimestre_actual) == $i)>{{ $i }}°</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tutor</label>
                    <select name="id_tutor"
                            class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <option value="">— Sin tutor asignado —</option>
                        @foreach($tutores as $t)
                            <option value="{{ $t->id_docente }}" @selected(old('id_tutor', $alumno->id_tutor) == $t->id_docente)>
                                {{ $t->nombre }} {{ $t->apellidos }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Padre / Tutor --}}
                @php $p = $alumno->padreTutor; @endphp
                <div class="pt-5 border-t dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">Datos del padre o tutor</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nombre(s)</label>
                            <input type="text" name="padre[nombre]" value="{{ old('padre.nombre', $p?->nombre) }}" maxlength="80"
                                   class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Apellidos</label>
                            <input type="text" name="padre[apellidos]" value="{{ old('padre.apellidos', $p?->apellidos) }}" maxlength="100"
                                   class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Correo electrónico</label>
                            <input type="email" name="padre[email]" value="{{ old('padre.email', $p?->email) }}" maxlength="150"
                                   class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Teléfono</label>
                            <input type="tel" name="padre[telefono]" value="{{ old('padre.telefono', $p?->telefono) }}" maxlength="20"
                                   class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">N° emergencia</label>
                            <input type="tel" name="padre[telefono_emergencia]" value="{{ old('padre.telefono_emergencia', $p?->telefono_emergencia) }}" maxlength="20"
                                   class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">INE (PDF)</label>
                            <input type="file" name="padre[ine]" accept="application/pdf"
                                   class="w-full text-xs text-gray-500 dark:text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 dark:file:bg-gray-700 dark:file:text-blue-300 hover:file:bg-blue-100">
                            @if($p?->ine_path)
                                <a href="{{ asset('storage/'.$p->ine_path) }}" target="_blank"
                                   class="text-xs text-[#0606F0] dark:text-blue-400 hover:underline mt-1 inline-flex items-center gap-1">
                                    <x-icon name="document" class="w-3.5 h-3.5" /> Ver INE actual
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Documentación --}}
                @php $docsByTipo = $alumno->documentos->keyBy('tipo'); @endphp
                <div class="pt-5 border-t dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-1">Documentación del alumno</h3>
                    <p class="text-xs text-gray-400 mb-4">Selecciona un archivo para reemplazar el actual.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach(\App\Models\DocumentoAlumno::TIPOS as $tipo => $label)
                            @php $doc = $docsByTipo[$tipo] ?? null; @endphp
                            <div class="p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                    @if($doc)
                                        <a href="{{ asset('storage/'.$doc->archivo_path) }}" target="_blank"
                                           class="text-xs text-[#0606F0] dark:text-blue-400 hover:underline">Ver actual</a>
                                    @endif
                                </div>
                                <input type="file" name="documentos[{{ $tipo }}]" accept="application/pdf"
                                       class="w-full text-xs text-gray-500 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 dark:file:bg-gray-700 dark:file:text-blue-300 hover:file:bg-blue-100">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pagos: solo informativo, no editable --}}
                <div class="pt-5 border-t dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-1">Bauchers de pago</h3>
                    <p class="text-xs text-gray-400 mb-3 inline-flex items-start gap-1">
                        <x-icon name="warning" class="w-4 h-4 flex-shrink-0 mt-0.5" />
                        <span>Los bauchers de pago no se pueden editar ni reemplazar desde aquí. Solo se pueden visualizar desde el detalle del alumno.</span>
                    </p>
                    <a href="{{ route('servicios.alumnos.show', $alumno) }}"
                       class="text-xs text-[#0606F0] dark:text-blue-400 hover:underline">Ver bauchers →</a>
                </div>

                <div class="flex gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="submit"
                            class="bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                        Guardar cambios
                    </button>
                    <a href="{{ route('servicios.alumnos.show', $alumno) }}"
                       class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
