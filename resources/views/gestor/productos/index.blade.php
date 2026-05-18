<x-panel title="Productos" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Productos de la Tienda</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Catálogo institucional para alumnos y docentes.
            </p>
        </div>
        <a href="{{ route('gestor.productos.create') }}"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo producto
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6 items-end bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm dark:shadow-gray-900/20 border border-transparent dark:border-gray-700">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                   placeholder="Nombre o código..."
                   class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Categoría</label>
            <select name="categoria" class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Todas</option>
                @foreach(\App\Models\Producto::CATEGORIAS as $key => $label)
                    <option value="{{ $key }}" @selected(request('categoria') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Estado</label>
            <select name="activo" class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Todos</option>
                <option value="1" @selected(request('activo') === '1')>Activos</option>
                <option value="0" @selected(request('activo') === '0')>Inactivos</option>
            </select>
        </div>
        <button type="submit" class="bg-[#0606F0] hover:bg-[#04276B] text-white px-4 py-2 rounded-lg text-sm font-medium">Filtrar</button>
        <a href="{{ route('gestor.productos.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2 px-2">Limpiar</a>
    </form>

    {{-- Grid de productos --}}
    @if($productos->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($productos as $p)
                <a href="{{ route('gestor.productos.show', $p) }}" class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow group">
                    {{-- Imagen --}}
                    <div class="aspect-square bg-gray-100 dark:bg-gray-700 relative overflow-hidden">
                        @if($p->imagen_principal)
                            <img src="{{ Storage::url($p->imagen_principal) }}" alt="{{ $p->nombre }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif

                        @if(!$p->activo)
                            <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-500 text-white">INACTIVO</span>
                        @endif
                        @if($p->stock_total <= 0)
                            <span class="absolute top-2 right-2 px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-900/80 text-white">SIN STOCK</span>
                        @elseif($p->variantes->where('stock_bajo', true)->count() > 0)
                            <span class="absolute top-2 right-2 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500 text-white">STOCK BAJO</span>
                        @endif
                    </div>

                    <div class="p-4">
                        <p class="text-[10px] font-mono text-gray-400 uppercase tracking-wide">{{ $p->codigo }}</p>
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm mt-1 line-clamp-2">{{ $p->nombre }}</h3>
                        <div class="flex items-center justify-between mt-3">
                            <span class="text-lg font-bold text-[#0606F0] dark:text-blue-400">${{ number_format($p->precio, 2) }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $p->stock_total }} en stock</span>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-2">{{ \App\Models\Producto::CATEGORIAS[$p->categoria] ?? $p->categoria }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $productos->links() }}</div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-12 text-center">
            <p class="text-sm text-gray-400">No hay productos registrados aún.</p>
            <a href="{{ route('gestor.productos.create') }}" class="inline-block mt-3 text-sm text-[#0606F0] hover:underline">Agregar el primero →</a>
        </div>
    @endif
</x-panel>
