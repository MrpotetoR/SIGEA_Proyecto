<x-panel title="Nuevo Alumno" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-2xl">
        <a href="{{ route('servicios.alumnos.index') }}"
           class="text-sm text-indigo-600 hover:underline mb-6 inline-block">← Volver a la lista</a>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-6">Datos del alumno</h2>

            <form method="POST" action="{{ route('servicios.alumnos.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre(s) *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="80"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                               title="Solo letras y espacios"
                               oninput="updateCount(this, 'cnt-nombre'); this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
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
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                               title="Solo letras y espacios"
                               oninput="updateCount(this, 'cnt-apellidos'); this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
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
                    <p class="text-xs text-gray-400 mt-1">Se usará como usuario de acceso. Contraseña inicial: <code>sigea{{ date('Y') }}</code></p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Carrera *</label>
                        <select name="id_carrera" required
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none @error('id_carrera') border-red-400 @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach($carreras as $c)
                                <option value="{{ $c->id_carrera }}" @selected(old('id_carrera') == $c->id_carrera)>
                                    {{ $c->nombre_carrera }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_carrera')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cuatrimestre *</label>
                        <select name="cuatrimestre_actual" required
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" @selected(old('cuatrimestre_actual', 1) == $i)>{{ $i }}°</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <x-ajax-select
                    name="id_tutor"
                    :url="route('ajax.docentes')"
                    label="Tutor"
                    placeholder="Buscar docente..."
                />

                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit"
                            class="bg-indigo-700 hover:bg-indigo-800 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                        Registrar alumno
                    </button>
                    <a href="{{ route('servicios.alumnos.index') }}"
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
// Inicializar contadores si hay valores previos (old())
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[maxlength][oninput]').forEach(el => el.dispatchEvent(new Event('input')));
});
</script>
