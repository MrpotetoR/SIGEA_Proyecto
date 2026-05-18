<x-panel title="Editar Noticia" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>
    <div class="max-w-2xl">
        <a href="{{ route('gestor.noticias.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">&larr; Volver</a>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
            <form method="POST" action="{{ route('gestor.noticias.update', $noticia) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titulo *</label>
                    <input type="text" name="titulo" value="{{ old('titulo', $noticia->titulo) }}" required
                           class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contenido *</label>
                    <textarea name="contenido" rows="6" required
                              class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">{{ old('contenido', $noticia->contenido) }}</textarea>
                </div>

                {{-- Imagen --}}
                <div x-data="{
                    modo: '{{ $noticia->imagen_url ? 'actual' : 'ninguno' }}',
                    preview: '{{ $noticia->imagen_url }}',
                    quitar: false
                }" class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Imagen</label>

                    {{-- Imagen actual --}}
                    <template x-if="modo === 'actual' && preview">
                        <div class="relative inline-block">
                            <img :src="preview" class="rounded-lg max-h-40 object-cover border dark:border-gray-700" alt="Imagen actual">
                            <button type="button" @click="modo = 'ninguno'; quitar = true; preview = null"
                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full text-xs flex items-center justify-center shadow">&times;</button>
                        </div>
                    </template>
                    <input type="hidden" name="quitar_imagen" :value="quitar ? '1' : '0'">

                    <div class="flex gap-2" x-show="modo !== 'actual'">
                        <button type="button" @click="modo = 'archivo'; preview = null; quitar = true"
                                :class="modo === 'archivo' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-blue-300 dark:border-blue-700' : 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-600'"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors">Subir archivo</button>
                        <button type="button" @click="modo = 'url'; preview = null; quitar = true"
                                :class="modo === 'url' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-blue-300 dark:border-blue-700' : 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-600'"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors">URL externa</button>
                    </div>
                    <div x-show="modo === 'archivo'" x-transition>
                        <input type="file" name="imagen" accept="image/*"
                               @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                               class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Max 512 KB · JPG, PNG, WEBP</p>
                    </div>
                    <div x-show="modo === 'url'" x-transition>
                        <input type="url" name="imagen_url" placeholder="https://ejemplo.com/imagen.jpg"
                               @input="preview = $event.target.value"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    </div>
                    <template x-if="preview && modo !== 'actual'">
                        <img :src="preview" class="mt-2 rounded-lg max-h-40 object-cover border dark:border-gray-700" alt="Vista previa">
                    </template>
                    @error('imagen')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    @error('imagen_url')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- PDF adjunto (opcional) --}}
                <div x-data="{
                        tienePdf: {{ $noticia->pdf_url ? 'true' : 'false' }},
                        quitar:   false,
                        nuevoArchivo: null
                    }">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Documento PDF <span class="text-gray-400 font-normal">(opcional)</span>
                    </label>
                    <input type="hidden" name="quitar_pdf" :value="quitar ? '1' : '0'">

                    {{-- PDF actual --}}
                    <template x-if="tienePdf && !quitar && !nuevoArchivo">
                        <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/40 mb-2">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <a href="{{ $noticia->pdf_url }}" target="_blank"
                                   class="text-sm font-medium text-[#0606F0] dark:text-blue-400 hover:underline truncate block">
                                    {{ $noticia->pdf_nombre ?? 'Documento adjunto' }}
                                </a>
                                <span class="text-[10px] text-gray-400">PDF actual</span>
                            </div>
                            <button type="button" @click="quitar = true"
                                    class="text-xs text-red-600 dark:text-red-400 hover:underline flex-shrink-0">
                                Quitar
                            </button>
                        </div>
                    </template>

                    {{-- Upload nuevo --}}
                    <div x-show="!tienePdf || quitar || nuevoArchivo" class="space-y-2">
                        <input type="file" name="pdf" accept="application/pdf"
                               @change="nuevoArchivo = $event.target.files[0]?.name || null"
                               class="block w-full text-xs text-gray-600 dark:text-gray-300
                                      file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                      file:text-xs file:font-medium
                                      file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-900/30 dark:file:text-blue-300
                                      hover:file:bg-blue-100 cursor-pointer
                                      @error('pdf') ring-1 ring-red-400 @enderror">
                        <input type="text" name="pdf_nombre" value="{{ old('pdf_nombre', $noticia->pdf_nombre) }}" maxlength="150"
                               placeholder="Nombre legible (ej: Reglamento Académico 2026)"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    </div>

                    <p class="text-[10px] text-gray-400 mt-1">PDF de hasta 10 MB.</p>
                    @error('pdf')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Programación de publicación --}}
                @php
                    $defTipo  = old('tipo_publicacion', $noticia->fecha_publicacion && $noticia->fecha_publicacion->isFuture() ? 'programada' : 'inmediata');
                    $defFecha = old('fecha_publicacion', optional($noticia->fecha_publicacion)->toDateString() ?? today()->toDateString());
                    $defHora  = old('hora_publicacion', optional($noticia->fecha_publicacion)->format('H:i') ?? now()->addMinutes(30)->format('H:i'));
                @endphp
                <div x-data="{
                        tipo: '{{ $defTipo }}',
                        fecha: '{{ $defFecha }}',
                        hora:  '{{ $defHora }}',
                        get hoy() { return new Date().toISOString().split('T')[0]; },
                        get minHora() {
                            if (this.fecha === this.hoy) {
                                const n = new Date(); n.setMinutes(n.getMinutes() + 1);
                                return n.toTimeString().slice(0,5);
                            }
                            return '00:00';
                        },
                        get advertencia() {
                            if (this.tipo !== 'programada') return '';
                            if (!this.fecha) return 'Selecciona una fecha.';
                            if (this.fecha < this.hoy) return 'La fecha no puede ser anterior a hoy.';
                            if (this.fecha === this.hoy && this.hora && this.hora <= new Date().toTimeString().slice(0,5))
                                return 'La hora debe ser posterior a la hora actual.';
                            return '';
                        }
                     }"
                     class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Publicación *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 text-sm cursor-pointer border rounded-lg px-3 py-2"
                               :class="tipo === 'inmediata' ? 'border-[#0606F0] bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600'">
                            <input type="radio" name="tipo_publicacion" value="inmediata" x-model="tipo" class="text-[#0606F0]">
                            <span>Publicar inmediatamente</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer border rounded-lg px-3 py-2"
                               :class="tipo === 'programada' ? 'border-[#0606F0] bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600'">
                            <input type="radio" name="tipo_publicacion" value="programada" x-model="tipo" class="text-[#0606F0]">
                            <span>Programar publicación</span>
                        </label>
                    </div>
                    <div x-show="tipo === 'programada'" x-transition class="grid grid-cols-2 gap-3 pt-2">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Fecha *</label>
                            <input type="date" name="fecha_publicacion" x-model="fecha" :min="hoy" :disabled="tipo !== 'programada'"
                                   class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            @error('fecha_publicacion')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Hora *</label>
                            <input type="time" name="hora_publicacion" x-model="hora" :min="minHora" max="23:59" :disabled="tipo !== 'programada'"
                                   class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            @error('hora_publicacion')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <p x-show="advertencia" x-text="advertencia" class="text-xs text-red-600 dark:text-red-400"></p>
                </div>

                {{-- Audiencia --}}
                <div x-data="{ audiencia: '{{ old('audiencia', is_array($noticia->destinatarios) ? 'roles' : 'todos') }}' }">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Audiencia de notificación</label>
                    <div class="space-y-3">
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input type="radio" name="audiencia" value="todos" x-model="audiencia" class="text-[#0606F0]">
                            <span>Todos los usuarios</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input type="radio" name="audiencia" value="roles" x-model="audiencia" class="text-[#0606F0]">
                            <span>Roles específicos</span>
                        </label>
                        <div x-show="audiencia === 'roles'" x-transition class="ml-6 space-y-2 border-l-2 border-[#0606F0]/20 pl-4">
                            @foreach ([
                                'gestor_escolar' => 'Gestores Escolares',
                                'docente'             => 'Docentes',
                                'alumno'              => 'Alumnos',
                            ] as $rol => $label)
                                @php $rolesActuales = old('roles', $noticia->destinatarios ?? []); @endphp
                                <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                                    <input type="checkbox" name="roles[]" value="{{ $rol }}"
                                           @checked(is_array($rolesActuales) && in_array($rol, $rolesActuales))
                                           class="rounded border-gray-300 dark:border-gray-600 text-[#0606F0] focus:ring-blue-500 dark:bg-gray-700">
                                    {{ $label }}
                                </label>
                            @endforeach
                            @error('roles')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="submit" class="bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">Guardar</button>
                    <a href="{{ route('gestor.noticias.index') }}" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
