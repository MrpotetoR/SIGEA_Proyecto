<x-panel title="Nuevo Producto" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="max-w-4xl">
        <a href="{{ route('gestor.productos.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-4 inline-block">← Volver</a>

        @php $tallasPredefinidas = \App\Http\Controllers\Gestor\ProductosController::TALLAS_DISPONIBLES; @endphp
        <form method="POST" action="{{ route('gestor.productos.store') }}" enctype="multipart/form-data"
              class="space-y-6"
              x-data="{
                  categoria: '{{ old('categoria', '') }}',
                  tallasDisponibles: {{ json_encode($tallasPredefinidas) }},
                  tallasSeleccionadas: {{ json_encode(old('tallas') ?? []) }},
                  stocks: {{ json_encode(old('stocks') ?? []) }},
                  get aceptaTallas() {
                      return this.categoria === 'uniforme';
                  },
                  toggleTalla(t) {
                      const i = this.tallasSeleccionadas.indexOf(t);
                      if (i >= 0) {
                          this.tallasSeleccionadas.splice(i, 1);
                          this.stocks.splice(i, 1);
                      } else {
                          this.tallasSeleccionadas.push(t);
                          this.stocks.push(0);
                      }
                  },
                  estaSeleccionada(t) {
                      return this.tallasSeleccionadas.includes(t);
                  },
                  indiceTalla(t) {
                      return this.tallasSeleccionadas.indexOf(t);
                  },
              }">
            @csrf

            {{-- Datos básicos --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Información del producto</h3>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código *</label>
                        <input type="text" name="codigo" value="{{ old('codigo') }}" maxlength="30" required
                               oninput="this.value = this.value.toUpperCase()"
                               class="w-full border rounded-lg px-3 py-2 text-sm font-mono uppercase focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 @error('codigo') border-red-400 @enderror"
                               placeholder="PLAY-AZUL">
                        @error('codigo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" maxlength="150" required
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 @error('nombre') border-red-400 @enderror"
                               placeholder="Playera Polo UDEA">
                        @error('nombre')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoría *</label>
                        <select name="categoria" required x-model="categoria"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                            <option value="">Seleccionar...</option>
                            @foreach(\App\Models\Producto::CATEGORIAS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1" x-show="categoria === 'uniforme'" x-cloak>Los uniformes manejan tallas.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Precio *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400">$</span>
                            <input type="number" name="precio" value="{{ old('precio') }}" min="0" step="0.01" max="999999.99" required
                                   class="w-full border rounded-lg pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Descripción <span class="text-gray-400 font-normal text-xs">(opcional)</span>
                    </label>
                    <textarea name="descripcion" rows="3" maxlength="2000"
                              class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">{{ old('descripcion') }}</textarea>
                </div>
            </div>

            {{-- Variantes / Tallas (solo categoria=uniforme) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Variantes y stock inicial</h3>

                <input type="hidden" name="tiene_tallas" :value="aceptaTallas ? 1 : 0">

                {{-- Sin categoría seleccionada --}}
                <div x-show="!categoria" class="text-sm text-gray-400 italic py-2">
                    Selecciona la categoría arriba para configurar las variantes.
                </div>

                {{-- Categoría que NO usa tallas --}}
                <div x-show="categoria && !aceptaTallas" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock inicial</label>
                    <input type="number" name="stocks[]" value="{{ old('stocks.0', 0) }}" min="0" max="9999"
                           class="w-40 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    <p class="text-[10px] text-gray-400 mt-1">Esta categoría no usa tallas. Se creará una variante única.</p>
                </div>

                {{-- Categoría que SÍ usa tallas (uniforme) --}}
                <div x-show="categoria && aceptaTallas" x-cloak>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                        Selecciona las tallas que tendrá este producto y captura el stock inicial de cada una.
                    </p>

                    {{-- Selector visual tipo "chips" --}}
                    <div class="flex flex-wrap gap-2 mb-4">
                        <template x-for="t in tallasDisponibles" :key="t">
                            <button type="button" @click="toggleTalla(t)"
                                    :class="estaSeleccionada(t)
                                        ? 'bg-[#0606F0] border-[#0606F0] text-white'
                                        : 'bg-white border-gray-300 text-gray-700 hover:border-[#0606F0] dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300'"
                                    class="min-w-[48px] px-3 py-1.5 rounded-lg border-2 text-sm font-semibold transition-colors">
                                <span x-text="t"></span>
                            </button>
                        </template>
                    </div>

                    {{-- Tabla con tallas seleccionadas --}}
                    <div x-show="tallasSeleccionadas.length > 0" x-cloak>
                        <table class="w-full text-sm">
                            <thead class="text-xs uppercase text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                                <tr>
                                    <th class="text-left pb-2 pr-4">Talla</th>
                                    <th class="text-left pb-2">Stock inicial</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(t, i) in tallasSeleccionadas" :key="t">
                                    <tr>
                                        <td class="py-2 pr-4">
                                            <input type="hidden" :name="`tallas[${i}]`" :value="t">
                                            <span class="inline-flex px-3 py-1 rounded-full bg-[#0606F0]/10 text-[#0606F0] font-bold text-sm" x-text="t"></span>
                                        </td>
                                        <td class="py-2">
                                            <input type="number" :name="`stocks[${i}]`" x-model.number="stocks[i]" min="0" max="9999" required
                                                   class="w-28 border rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Imágenes --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Imágenes</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Imagen principal <span class="text-gray-400 font-normal text-xs">(portada del catálogo)</span></label>
                    <input type="file" name="imagen_principal" accept="image/*"
                           class="block w-full text-xs text-gray-600 dark:text-gray-300
                                  file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                  file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100 cursor-pointer">
                    @error('imagen_principal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Galería <span class="text-gray-400 font-normal text-xs">(opcional, hasta 6 imágenes)</span></label>
                    <input type="file" name="galeria[]" accept="image/*" multiple
                           class="block w-full text-xs text-gray-600 dark:text-gray-300
                                  file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                  file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-[10px] text-gray-400 mt-1">JPG, PNG hasta 5 MB cada una.</p>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    Crear producto
                </button>
                <a href="{{ route('gestor.productos.index') }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-panel>
