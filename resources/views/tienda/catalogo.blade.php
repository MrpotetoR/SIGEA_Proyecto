@php
    $user = auth()->user();
    $panelNombre = match (true) {
        $user->hasRole('alumno')  => 'Panel Alumno',
        $user->hasRole('docente') => 'Panel Docente',
        default                   => 'UDEA',
    };
    $navPartial = match (true) {
        $user->hasRole('alumno')  => 'partials.alumno-nav',
        $user->hasRole('docente') => 'partials.docente-nav',
        default                   => null,
    };
@endphp

<x-panel title="Tienda Institucional" :panelNombre="$panelNombre">
    @if($navPartial)
        <x-slot name="nav">@include($navPartial)</x-slot>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Tienda Institucional UDEA</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Uniformes, credenciales y accesorios oficiales.
                {{ $nivel === 'bachillerato' ? 'Bachillerato' : 'Universidad' }}.
            </p>
        </div>
        <a href="{{ route('tienda.carrito') }}"
           class="inline-flex items-center gap-2 bg-[#0606F0] hover:bg-[#04276B] text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors relative">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Carrito
            @if($carrito['count'] > 0)
                <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-amber-500 text-white rounded-full text-[10px] font-bold flex items-center justify-center">
                    {{ $carrito['count'] }}
                </span>
            @endif
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6 items-end bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm dark:shadow-gray-900/20 border border-transparent dark:border-gray-700">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                   placeholder="Nombre del producto..."
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
        <button type="submit" class="bg-[#0606F0] hover:bg-[#04276B] text-white px-4 py-2 rounded-lg text-sm font-medium">Filtrar</button>
        <a href="{{ route('tienda.pedidos') }}" class="ml-auto text-sm text-[#0606F0] dark:text-blue-400 hover:underline">Mis pedidos →</a>
    </form>

    @if($productos->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($productos as $p)
                <a href="{{ route('tienda.detalle', $p) }}"
                   class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 overflow-hidden hover:shadow-md transition-all group">
                    <div class="aspect-square bg-gray-100 dark:bg-gray-700 relative overflow-hidden">
                        @if($p->imagen_principal)
                            <img src="{{ Storage::url($p->imagen_principal) }}" alt="{{ $p->nombre }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                        @endif
                        @if($p->stock_total <= 0)
                            <span class="absolute top-2 right-2 px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-900/80 text-white">SIN STOCK</span>
                        @endif
                    </div>
                    <div class="p-4">
                        <p class="text-[10px] uppercase text-gray-400 tracking-wide">{{ \App\Models\Producto::CATEGORIAS[$p->categoria] ?? '' }}</p>
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm mt-1 line-clamp-2">{{ $p->nombre }}</h3>
                        <div class="flex items-center justify-between mt-3">
                            <span class="text-lg font-bold text-[#0606F0] dark:text-blue-400">${{ number_format($p->precio, 2) }}</span>
                            @if($p->stock_total > 0)
                                <span class="text-[11px] text-green-600 dark:text-green-400 font-semibold">Disponible</span>
                            @else
                                <span class="text-[11px] text-red-500 font-semibold">Agotado</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $productos->links() }}</div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-12 text-center">
            <p class="text-sm text-gray-400">No hay productos disponibles en este momento.</p>
        </div>
    @endif
</x-panel>
