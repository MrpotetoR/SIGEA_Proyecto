<x-panel title="Editar Noticia" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>
    <div class="max-w-2xl">
        <a href="{{ route('servicios.noticias.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">&larr; Volver</a>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
            <form method="POST" action="{{ route('servicios.noticias.update', $noticia) }}" enctype="multipart/form-data" class="space-y-5">
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
                                'servicios_escolares' => 'Servicios Escolares',
                                'director_carrera'    => 'Directores de Carrera',
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
                    <a href="{{ route('servicios.noticias.index') }}" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
