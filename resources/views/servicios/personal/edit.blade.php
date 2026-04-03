<x-panel title="Editar Personal" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-2xl">
        <a href="{{ route('servicios.personal.show', $personal) }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">← Volver</a>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-6">Editar personal</h2>
            @php
                $parts = explode(' ', $personal->name, 2);
                $nombre = old('nombre', $parts[0] ?? '');
                $apellidos = old('apellidos', $parts[1] ?? '');
            @endphp
            <form method="POST" action="{{ route('servicios.personal.update', $personal) }}" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre(s) *</label>
                        <input type="text" name="nombre" value="{{ $nombre }}" required maxlength="80"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                               title="Solo letras y espacios"
                               oninput="updateCount(this, 'cnt-nombre'); this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('nombre') border-red-400 @enderror">
                        <div class="flex justify-between mt-1">
                            @error('nombre')
                                <p class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</p>
                            @else
                                <span></span>
                            @enderror
                            <span id="cnt-nombre" class="text-xs text-gray-400 dark:text-gray-400">0/80</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Apellidos *</label>
                        <input type="text" name="apellidos" value="{{ $apellidos }}" required maxlength="100"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                               title="Solo letras y espacios"
                               oninput="updateCount(this, 'cnt-apellidos'); this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('apellidos') border-red-400 @enderror">
                        <div class="flex justify-between mt-1">
                            @error('apellidos')
                                <p class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</p>
                            @else
                                <span></span>
                            @enderror
                            <span id="cnt-apellidos" class="text-xs text-gray-400 dark:text-gray-400">0/100</span>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correo electrónico *</label>
                    <input type="email" name="email" value="{{ old('email', $personal->email) }}" required maxlength="255"
                           class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="submit"
                            class="bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                        Guardar cambios
                    </button>
                    <a href="{{ route('servicios.personal.show', $personal) }}"
                       class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
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
