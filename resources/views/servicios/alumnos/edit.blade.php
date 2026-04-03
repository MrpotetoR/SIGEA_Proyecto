<x-panel title="Editar Alumno" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-2xl">
        <a href="{{ route('servicios.alumnos.show', $alumno) }}"
           class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">← Volver al perfil</a>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-6">Editar datos — {{ $alumno->matricula }}</h2>

            <form method="POST" action="{{ route('servicios.alumnos.update', $alumno) }}" class="space-y-5">
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

                <x-ajax-select
                    name="id_tutor"
                    :url="route('ajax.docentes')"
                    label="Tutor"
                    placeholder="Buscar docente..."
                    :value="$alumno->id_tutor"
                    :display="$alumno->tutor?->nombre_completo ?? ''"
                />

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
