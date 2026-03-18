<x-panel title="Nuevo Docente" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-2xl">
        <a href="{{ route('servicios.docentes.index') }}" class="text-sm text-indigo-600 hover:underline mb-6 inline-block">← Volver</a>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-6">Datos del docente</h2>
            <form method="POST" action="{{ route('servicios.docentes.store') }}" class="space-y-5">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre(s) *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="80"
                               oninput="updateCount(this, 'cnt-nombre')"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none @error('nombre') border-red-400 @enderror">
                        <div class="flex justify-between mt-1">
                            @error('nombre')
                                <p class="text-red-500 text-xs">{{ $message }}</p>
                            @else
                                <span></span>
                            @enderror
                            <span id="cnt-nombre" class="text-xs text-gray-400">0/80</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos') }}" required maxlength="100"
                               oninput="updateCount(this, 'cnt-apellidos')"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none @error('apellidos') border-red-400 @enderror">
                        <div class="flex justify-between mt-1">
                            @error('apellidos')
                                <p class="text-red-500 text-xs">{{ $message }}</p>
                            @else
                                <span></span>
                            @enderror
                            <span id="cnt-apellidos" class="text-xs text-gray-400">0/100</span>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required maxlength="255"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-400 mt-1">Contraseña inicial: <code>docente{{ date('Y') }}</code></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                        <input type="text" name="especialidad" value="{{ old('especialidad') }}" maxlength="100"
                               oninput="updateCount(this, 'cnt-especialidad')"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        <div class="flex justify-end mt-1">
                            <span id="cnt-especialidad" class="text-xs text-gray-400">0/100</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de contrato *</label>
                        <div class="flex gap-4 mt-2 mb-2">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="tipo_contrato" value="horas"
                                       @checked(old('tipo_contrato', 'horas') === 'horas')
                                       onchange="document.getElementById('campo-horas').classList.remove('hidden')"
                                       class="text-indigo-600 focus:ring-indigo-500">
                                Por horas
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="tipo_contrato" value="planta"
                                       @checked(old('tipo_contrato') === 'planta')
                                       onchange="document.getElementById('campo-horas').classList.add('hidden')"
                                       class="text-indigo-600 focus:ring-indigo-500">
                                Docente de Planta
                            </label>
                        </div>
                        <div id="campo-horas" class="{{ old('tipo_contrato') === 'planta' ? 'hidden' : '' }}">
                            <input type="number" name="horas_contrato" value="{{ old('horas_contrato') }}"
                                   min="1" max="40" placeholder="Ej. 20"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                            <p class="text-xs text-gray-400 mt-1">Entre 1 y 40 horas semanales</p>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="es_tutor" value="1" @checked(old('es_tutor'))
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        Es tutor de grupo
                    </label>
                </div>
                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit"
                            class="bg-indigo-700 hover:bg-indigo-800 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                        Registrar docente
                    </button>
                    <a href="{{ route('servicios.docentes.index') }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-panel>

<script>
function updateCount(input, counterId) {
    const counter = document.getElementById(counterId);
    const max = input.maxLength;
    const len = input.value.length;
    counter.textContent = len + '/' + max;
    counter.classList.toggle('text-red-500', len >= max);
    counter.classList.toggle('text-gray-400', len < max);
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[maxlength][oninput]').forEach(el => el.dispatchEvent(new Event('input')));
});
</script>
