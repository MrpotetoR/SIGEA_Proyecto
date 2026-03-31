<x-panel title="Noticias" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="flex justify-end mb-6">
        <a href="{{ route('servicios.noticias.create') }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            + Publicar noticia
        </a>
    </div>

    <div class="space-y-4">
        @forelse($noticias as $n)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-5 flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $n->titulo }}</h3>
                        @if(!$n->activa)
                            <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 flex-shrink-0">Inactiva</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ Str::limit($n->contenido, 120) }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mt-2">
                        {{ $n->fecha_publicacion->format('d/m/Y') }} · {{ $n->autor?->name }}
                    </p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <a href="{{ route('servicios.noticias.edit', $n) }}"
                       class="bg-yellow-100 dark:bg-yellow-900/30 hover:bg-yellow-200 dark:hover:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">Editar</a>
                    <form method="POST" action="{{ route('servicios.noticias.destroy', $n) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 text-red-800 dark:text-red-300 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors"
                                onclick="return confirm('¿Eliminar noticia?')">Eliminar</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-12 text-center text-gray-400 dark:text-gray-400">No hay noticias publicadas.</div>
        @endforelse
        @if($noticias instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div>{{ $noticias->links() }}</div>
        @endif
    </div>
</x-panel>
