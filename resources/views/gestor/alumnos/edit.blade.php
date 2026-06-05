<x-panel title="Editar Alumno" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="max-w-6xl">
        <a href="{{ route('gestor.alumnos.show', $alumno) }}"
           class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">← Volver al perfil</a>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-6">Editar datos — {{ $alumno->id_alumno_publico }}</h2>

            <form method="POST" action="{{ route('gestor.alumnos.update', $alumno) }}" class="space-y-5" enctype="multipart/form-data">
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
                    @if($esBachi)
                        {{-- ─── Bachillerato: Plan ─── --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plan de Bachillerato *</label>
                            <select name="id_plan_bachillerato" required
                                    class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:outline-none">
                                @foreach($planesBachi as $p)
                                    <option value="{{ $p->id_plan_bachillerato }}" @selected(old('id_plan_bachillerato', $alumno->id_plan_bachillerato) == $p->id_plan_bachillerato)>
                                        {{ $p->nombre_plan }} ({{ $p->duracion_texto }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $alumno->planBachillerato?->label_periodo ?? 'Semestre' }} actual *</label>
                            @php $maxPeriodos = $alumno->planBachillerato?->num_semestres ?? 6; @endphp
                            <select name="cuatrimestre_actual" required
                                    class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:outline-none">
                                @for($i = 1; $i <= $maxPeriodos; $i++)
                                    <option value="{{ $i }}" @selected(old('cuatrimestre_actual', $alumno->cuatrimestre_actual) == $i)>{{ $i }}°</option>
                                @endfor
                            </select>
                            <p class="text-[10px] text-gray-400 mt-1">{{ $alumno->planBachillerato?->label_periodo ?? 'Periodo' }} — {{ $maxPeriodos }} en total en este plan</p>
                        </div>
                    @else
                        {{-- ─── Universidad: Carrera ─── --}}
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $alumno->carrera?->label_periodo ?? 'Cuatrimestre' }} actual *</label>
                            @php $maxPeriodos = $alumno->carrera?->max_periodos ?? 10; @endphp
                            <select name="cuatrimestre_actual" required
                                    class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                                @for($i = 1; $i <= $maxPeriodos; $i++)
                                    <option value="{{ $i }}" @selected(old('cuatrimestre_actual', $alumno->cuatrimestre_actual) == $i)>{{ $i }}°</option>
                                @endfor
                            </select>
                            <p class="text-[10px] text-gray-400 mt-1">{{ $alumno->carrera?->label_periodo ?? 'Periodo' }} — {{ $maxPeriodos }} en total para esta carrera</p>
                        </div>
                    @endif
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
                    <p class="text-xs text-gray-400 mb-4">Sube, reemplaza o elimina archivos. Formato permitido: <span class="font-semibold">PDF</span> (máx. 5 MB).</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach(\App\Models\DocumentoAlumno::TIPOS as $tipo => $label)
                            @php $doc = $docsByTipo[$tipo] ?? null; @endphp
                            <div class="p-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50">
                                <div class="flex items-center justify-between mb-2 gap-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                    @if($doc)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 flex-shrink-0">Cargado</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-200 text-gray-600 dark:bg-gray-600 dark:text-gray-300 flex-shrink-0">Pendiente</span>
                                    @endif
                                </div>
                                @if($doc)
                                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                                        <a href="{{ asset('storage/'.$doc->archivo_path) }}" target="_blank"
                                           class="text-xs text-[#0606F0] dark:text-blue-400 hover:underline font-medium">Ver actual</a>
                                        <span class="text-gray-300 dark:text-gray-600">|</span>
                                        <a href="{{ asset('storage/'.$doc->archivo_path) }}" download
                                           class="text-xs text-green-700 dark:text-green-400 hover:underline font-medium">Descargar</a>
                                        <span class="text-gray-300 dark:text-gray-600">|</span>
                                        <button type="button"
                                                onclick="udeaConfirm({
                                                    title: 'Eliminar documento',
                                                    message: '¿Eliminar este documento?',
                                                    detail: 'Podrás volver a cargarlo después.',
                                                    variant: 'danger',
                                                    icon: 'trash',
                                                    confirmText: 'Eliminar',
                                                    cancelText: 'Cancelar'
                                                }).then(ok => { if (ok) document.getElementById('del-al-{{ $tipo }}').submit(); });"
                                                class="text-xs text-red-600 dark:text-red-400 hover:underline font-medium">Eliminar</button>
                                    </div>
                                @endif
                                <input type="file" name="documentos[{{ $tipo }}]" accept="application/pdf"
                                       class="w-full text-xs text-gray-500 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 dark:file:bg-gray-700 dark:file:text-blue-300 hover:file:bg-blue-100 @error('documentos.'.$tipo) ring-1 ring-red-400 @enderror">
                                <p class="text-[10px] text-gray-400 dark:text-gray-400 mt-1">
                                    {{ $doc ? 'Selecciona un archivo PDF para reemplazar el actual.' : 'Selecciona un PDF para subir.' }}
                                </p>
                                @error('documentos.'.$tipo)
                                    <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pagos: solo informativo, no editable --}}
                <div class="pt-5 border-t dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-1">Váuchers de pago</h3>
                    <p class="text-xs text-gray-400 mb-3 inline-flex items-start gap-1">
                        <x-icon name="warning" class="w-4 h-4 flex-shrink-0 mt-0.5" />
                        <span>Los váuchers de pago no se pueden editar ni reemplazar desde aquí. Solo se pueden visualizar desde el detalle del alumno.</span>
                    </p>
                    <a href="{{ route('gestor.alumnos.show', $alumno) }}"
                       class="text-xs text-[#0606F0] dark:text-blue-400 hover:underline">Ver váuchers →</a>
                </div>

                <div class="flex gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="submit"
                            class="bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                        Guardar cambios
                    </button>
                    <a href="{{ route('gestor.alumnos.show', $alumno) }}"
                       class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>

            {{-- Formularios ocultos para eliminar documentos del alumno --}}
            @foreach($docsByTipo as $tipo => $doc)
                <form id="del-al-{{ $tipo }}" method="POST" action="{{ route('gestor.alumnos.documentos.destroy', $doc) }}" class="hidden">
                    @csrf @method('DELETE')
                </form>
            @endforeach
        </div>
    </div>
</x-panel>
