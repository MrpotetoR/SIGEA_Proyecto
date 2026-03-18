<x-panel title="Editar Docente" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-2xl">
        <a href="{{ route('servicios.docentes.show', $docente) }}" class="text-sm text-indigo-600 hover:underline mb-6 inline-block">← Volver</a>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-6">Editar docente</h2>
            <form method="POST" action="{{ route('servicios.docentes.update', $docente) }}" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre(s) *</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $docente->nombre) }}" required
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos', $docente->apellidos) }}" required
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                        <input type="text" name="especialidad" value="{{ old('especialidad', $docente->especialidad) }}"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de contrato</label>
                        @php $esPlanta = is_null($docente->horas_contrato); @endphp
                        <div class="flex gap-4 mt-2 mb-2">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="tipo_contrato" value="horas"
                                       @checked(old('tipo_contrato', $esPlanta ? 'planta' : 'horas') === 'horas')
                                       onchange="document.getElementById('campo-horas').classList.remove('hidden')"
                                       class="text-indigo-600 focus:ring-indigo-500">
                                Por horas
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="tipo_contrato" value="planta"
                                       @checked(old('tipo_contrato', $esPlanta ? 'planta' : 'horas') === 'planta')
                                       onchange="document.getElementById('campo-horas').classList.add('hidden')"
                                       class="text-indigo-600 focus:ring-indigo-500">
                                Docente de Planta
                            </label>
                        </div>
                        <div id="campo-horas" class="{{ ($esPlanta && old('tipo_contrato', 'planta') === 'planta') ? 'hidden' : '' }}">
                            <input type="number" name="horas_contrato" value="{{ old('horas_contrato', $docente->horas_contrato) }}"
                                   min="1" max="40" placeholder="Ej. 20"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                            <p class="text-xs text-gray-400 mt-1">Entre 1 y 40 horas semanales</p>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="es_tutor" value="1" @checked(old('es_tutor', $docente->es_tutor))
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        Es tutor de grupo
                    </label>
                </div>
                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit"
                            class="bg-indigo-700 hover:bg-indigo-800 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                        Guardar cambios
                    </button>
                    <a href="{{ route('servicios.docentes.show', $docente) }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
