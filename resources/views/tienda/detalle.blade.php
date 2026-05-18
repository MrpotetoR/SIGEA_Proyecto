@php
    $user = auth()->user();
    $panelNombre = $user->hasRole('alumno') ? 'Panel Alumno' : 'Panel Docente';
    $navPartial = $user->hasRole('alumno') ? 'partials.alumno-nav' : 'partials.docente-nav';
@endphp

<x-panel title="Producto" :panelNombre="$panelNombre">
    <x-slot name="nav">@include($navPartial)</x-slot>

    <div class="max-w-5xl">
        <a href="{{ route('tienda.catalogo') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-4 inline-block">← Volver al catálogo</a>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-data="{
            varianteSel: {{ $producto->variantes->where('stock', '>', 0)->first()?->id_variante ?? 'null' }},
            cantidad: 1,
            imagenActiva: '{{ $producto->imagen_principal ? Storage::url($producto->imagen_principal) : '' }}',
            get stockSeleccionado() {
                if (!this.varianteSel) return 0;
                const v = {{ json_encode($producto->variantes->mapWithKeys(fn($v) => [$v->id_variante => $v->stock])) }};
                return v[this.varianteSel] ?? 0;
            }
        }">
            {{-- Galería --}}
            <div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 overflow-hidden aspect-square mb-3">
                    <template x-if="imagenActiva">
                        <img :src="imagenActiva" alt="{{ $producto->nombre }}" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!imagenActiva">
                        <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-50 dark:bg-gray-700">
                            <svg class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                    </template>
                </div>

                @if($producto->imagenes->isNotEmpty())
                    <div class="grid grid-cols-5 gap-2">
                        @if($producto->imagen_principal)
                            <button type="button" @click="imagenActiva = '{{ Storage::url($producto->imagen_principal) }}'"
                                    class="aspect-square rounded-lg border-2 overflow-hidden hover:border-[#0606F0]"
                                    :class="imagenActiva === '{{ Storage::url($producto->imagen_principal) }}' ? 'border-[#0606F0]' : 'border-transparent dark:border-gray-700'">
                                <img src="{{ Storage::url($producto->imagen_principal) }}" class="w-full h-full object-cover">
                            </button>
                        @endif
                        @foreach($producto->imagenes as $img)
                            <button type="button" @click="imagenActiva = '{{ Storage::url($img->archivo_path) }}'"
                                    class="aspect-square rounded-lg border-2 overflow-hidden hover:border-[#0606F0]"
                                    :class="imagenActiva === '{{ Storage::url($img->archivo_path) }}' ? 'border-[#0606F0]' : 'border-transparent dark:border-gray-700'">
                                <img src="{{ Storage::url($img->archivo_path) }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Info + acciones --}}
            <div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                    <p class="text-[10px] uppercase text-gray-400 tracking-wide">{{ \App\Models\Producto::CATEGORIAS[$producto->categoria] ?? '' }}</p>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $producto->nombre }}</h2>
                    <p class="text-[10px] font-mono text-gray-400 mt-1">{{ $producto->codigo }}</p>

                    <div class="mt-5 mb-5 pb-5 border-b dark:border-gray-700">
                        <span class="text-3xl font-bold text-[#0606F0] dark:text-blue-400">${{ number_format($producto->precio, 2) }}</span>
                    </div>

                    @if($producto->descripcion)
                        <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line mb-5">{{ $producto->descripcion }}</p>
                    @endif

                    <form method="POST" action="{{ route('tienda.carrito.agregar') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="id_variante" :value="varianteSel">

                        {{-- Selector de talla --}}
                        @if($producto->tiene_tallas)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Talla</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($producto->variantes as $v)
                                        <button type="button" @click="varianteSel = {{ $v->id_variante }}; cantidad = 1"
                                                :disabled="{{ $v->stock <= 0 ? 'true' : 'false' }}"
                                                class="min-w-[48px] px-3 py-2 rounded-lg border-2 text-sm font-semibold transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                                                :class="varianteSel === {{ $v->id_variante }}
                                                    ? 'border-[#0606F0] bg-[#0606F0]/10 text-[#0606F0]'
                                                    : 'border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-[#0606F0]'">
                                            {{ $v->talla }}
                                            @if($v->stock <= 0)
                                                <span class="block text-[9px] font-normal text-red-500">Agotado</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            @php $v = $producto->variantes->first(); @endphp
                            @if($v)
                                <input type="hidden" name="id_variante" value="{{ $v->id_variante }}">
                            @endif
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cantidad</label>
                            <div class="flex items-center gap-3">
                                <button type="button" @click="cantidad = Math.max(1, cantidad - 1)"
                                        class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 font-bold">−</button>
                                <input type="number" name="cantidad" x-model.number="cantidad" min="1" max="10" required readonly
                                       class="w-16 border rounded-lg px-2 py-2 text-center text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                <button type="button" @click="cantidad = Math.min(10, stockSeleccionado, cantidad + 1)"
                                        class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 font-bold">+</button>
                                <span class="text-xs text-gray-500" x-show="stockSeleccionado > 0">
                                    <span x-text="stockSeleccionado"></span> disponibles
                                </span>
                            </div>
                        </div>

                        <button type="submit" :disabled="!varianteSel || stockSeleccionado <= 0"
                                class="w-full bg-[#0606F0] hover:bg-[#04276B] disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-4 py-3 rounded-lg text-sm font-bold transition-colors">
                            <span x-show="varianteSel && stockSeleccionado > 0">Agregar al carrito</span>
                            <span x-show="!varianteSel || stockSeleccionado <= 0">Sin disponibilidad</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-panel>
