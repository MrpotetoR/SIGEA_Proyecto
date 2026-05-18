<x-panel title="Configuración — Tienda" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    <div class="max-w-3xl">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Configuración de la Tienda Institucional</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Datos bancarios y de entrega que verá el alumno al generar un pedido.
                Estos valores se aplican a Universidad y Bachillerato.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.configuracion.tienda.update') }}" class="space-y-6">
            @csrf @method('PUT')

            {{-- Cuenta bancaria --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#0606F0]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Cuenta bancaria institucional
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Banco *</label>
                        <input type="text" name="banco" value="{{ old('banco', $config['banco']) }}" required maxlength="80"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 @error('banco') border-red-400 @enderror"
                               placeholder="BBVA, Santander, Banorte...">
                        @error('banco')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titular *</label>
                        <input type="text" name="titular" value="{{ old('titular', $config['titular']) }}" required maxlength="150"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 @error('titular') border-red-400 @enderror">
                        @error('titular')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número de cuenta *</label>
                        <input type="text" name="numero" value="{{ old('numero', $config['numero']) }}" required maxlength="30"
                               class="w-full border rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 @error('numero') border-red-400 @enderror"
                               placeholder="0123 4567 8901">
                        @error('numero')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CLABE *</label>
                        <input type="text" name="clabe" value="{{ old('clabe', $config['clabe']) }}" required maxlength="18" minlength="18"
                               pattern="[0-9]{18}"
                               class="w-full border rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 @error('clabe') border-red-400 @enderror"
                               placeholder="18 dígitos">
                        @error('clabe')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Prefijo de referencia <span class="text-gray-400 font-normal text-xs">(opcional)</span>
                    </label>
                    <input type="text" name="referencia" value="{{ old('referencia', $config['referencia']) }}" maxlength="20"
                           class="w-full border rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                           placeholder="Ej: UDEA-">
                    <p class="text-[10px] text-gray-400 mt-1">El alumno usará: <code>{prefijo}{folio_pedido}</code>, ej. <code>UDEA-PED-2026-0001</code>.</p>
                </div>
            </div>

            {{-- Entrega --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#0606F0]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Punto de entrega
                </h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ubicación física *</label>
                    <input type="text" name="ubicacion" value="{{ old('ubicacion', $config['ubicacion']) }}" required maxlength="300"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 @error('ubicacion') border-red-400 @enderror">
                    @error('ubicacion')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Horario de atención *</label>
                    <input type="text" name="horario" value="{{ old('horario', $config['horario']) }}" required maxlength="200"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 @error('horario') border-red-400 @enderror"
                           placeholder="Lunes a Viernes de 9:00 a 17:00 hrs">
                    @error('horario')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Instrucciones --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Instrucciones adicionales para el alumno <span class="text-gray-400 font-normal text-xs">(opcional)</span>
                </label>
                <textarea name="instrucciones" rows="4" maxlength="1000"
                          class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">{{ old('instrucciones', $config['instrucciones']) }}</textarea>
                <p class="text-[10px] text-gray-400 mt-1">Se mostrará al alumno cuando genere su pedido.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-[#0606F0] hover:bg-[#04276B] text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    Guardar configuración
                </button>
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-panel>
