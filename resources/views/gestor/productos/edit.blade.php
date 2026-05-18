<x-panel title="Editar Producto" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="max-w-4xl">
        <a href="{{ route('gestor.productos.show', $producto) }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-4 inline-block">← Volver al detalle</a>

        <form method="POST" action="{{ route('gestor.productos.update', $producto) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Información del producto</h3>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código</label>
                        <input type="text" value="{{ $producto->codigo }}" disabled
                               class="w-full border bg-gray-50 dark:bg-gray-700/50 text-gray-500 rounded-lg px-3 py-2 text-sm font-mono">
                        <p class="text-[10px] text-gray-400 mt-1">No se puede modificar.</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre *</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" maxlength="150" required
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoría *</label>
                        <select name="categoria" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                            @foreach(\App\Models\Producto::CATEGORIAS as $key => $label)
                                <option value="{{ $key }}" @selected(old('categoria', $producto->categoria) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Precio *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400">$</span>
                            <input type="number" name="precio" value="{{ old('precio', $producto->precio) }}" min="0" step="0.01" max="999999.99" required
                                   class="w-full border rounded-lg pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                    <textarea name="descripcion" rows="3" maxlength="2000"
                              class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">{{ old('descripcion', $producto->descripcion) }}</textarea>
                </div>

                <label class="flex items-center gap-2 mt-4 cursor-pointer">
                    <input type="checkbox" name="activo" value="1" @checked(old('activo', $producto->activo))
                           class="w-4 h-4 rounded text-green-600 focus:ring-green-400">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Producto activo en el catálogo</span>
                </label>
            </div>

            {{-- Imagen principal --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">Imagen principal</h3>
                @if($producto->imagen_principal)
                    <img src="{{ Storage::url($producto->imagen_principal) }}" alt="" class="w-32 aspect-square object-cover rounded-lg border mb-3">
                @endif
                <input type="file" name="imagen_principal" accept="image/*"
                       class="block w-full text-xs text-gray-600 dark:text-gray-300
                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                              file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100 cursor-pointer">
                <p class="text-[10px] text-gray-400 mt-1">Selecciona una nueva imagen para reemplazar la actual.</p>
            </div>

            {{-- Agregar a galería --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">Agregar imágenes a la galería</h3>
                <input type="file" name="galeria[]" accept="image/*" multiple
                       class="block w-full text-xs text-gray-600 dark:text-gray-300
                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                              file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100 cursor-pointer">
                <p class="text-[10px] text-gray-400 mt-1">Para eliminar imágenes existentes, ve a la pantalla de detalle.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-[#0606F0] hover:bg-[#04276B] text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    Guardar cambios
                </button>
                <a href="{{ route('gestor.productos.show', $producto) }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-panel>
