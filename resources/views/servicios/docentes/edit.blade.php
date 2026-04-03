<x-panel title="Editar Docente" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-2xl">
        <a href="{{ route('servicios.docentes.show', $docente) }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">← Volver</a>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-6">Editar docente</h2>
            <form method="POST" action="{{ route('servicios.docentes.update', $docente) }}" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre(s) *</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $docente->nombre) }}" required maxlength="80"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                               title="Solo letras y espacios"
                               oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('nombre') border-red-400 @enderror">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Apellidos *</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos', $docente->apellidos) }}" required maxlength="100"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                               title="Solo letras y espacios"
                               oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('apellidos') border-red-400 @enderror">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Especialidad</label>
                        <input type="text" name="especialidad" value="{{ old('especialidad', $docente->especialidad) }}"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de contrato</label>
                        @php $esPlanta = is_null($docente->horas_contrato); @endphp
                        <div class="flex gap-4 mt-2 mb-2">
                            <label class="flex items-center gap-2 text-sm cursor-pointer dark:text-gray-300">
                                <input type="radio" name="tipo_contrato" value="horas"
                                       @checked(old('tipo_contrato', $esPlanta ? 'planta' : 'horas') === 'horas')
                                       onchange="document.getElementById('campo-horas').classList.remove('hidden')"
                                       class="text-[#0606F0] focus:ring-blue-500">
                                Por horas
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer dark:text-gray-300">
                                <input type="radio" name="tipo_contrato" value="planta"
                                       @checked(old('tipo_contrato', $esPlanta ? 'planta' : 'horas') === 'planta')
                                       onchange="document.getElementById('campo-horas').classList.add('hidden')"
                                       class="text-[#0606F0] focus:ring-blue-500">
                                Docente de Planta
                            </label>
                        </div>
                        <div id="campo-horas" class="{{ ($esPlanta && old('tipo_contrato', 'planta') === 'planta') ? 'hidden' : '' }}">
                            <input type="number" name="horas_contrato" value="{{ old('horas_contrato', $docente->horas_contrato) }}"
                                   min="1" max="40" placeholder="Ej. 20"
                                   class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            <p class="text-xs text-gray-400 dark:text-gray-400 mt-1">Entre 1 y 40 horas semanales</p>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="checkbox" name="es_tutor" value="1" @checked(old('es_tutor', $docente->es_tutor))
                               class="rounded border-gray-300 dark:border-gray-600 text-[#0606F0] focus:ring-blue-500">
                        Es tutor de grupo
                    </label>
                </div>
                <div class="flex gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="submit"
                            class="bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                        Guardar cambios
                    </button>
                    <a href="{{ route('servicios.docentes.show', $docente) }}"
                       class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
