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
            <div class="bg-white rounded-xl shadow p-5 flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                        <h3 class="font-semibold text-gray-900 truncate">{{ $n->titulo }}</h3>
                        @if(!$n->activa)
                            <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-500 flex-shrink-0">Inactiva</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 line-clamp-2">{{ Str::limit($n->contenido, 120) }}</p>
                    <p class="text-xs text-gray-400 mt-2">
                        {{ $n->fecha_publicacion->format('d/m/Y') }} · {{ $n->autor?->name }}
                    </p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <a href="{{ route('servicios.noticias.edit', $n) }}"
                       class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">Editar</a>
                    <form method="POST" action="{{ route('servicios.noticias.destroy', $n) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors"
                                onclick="return confirm('¿Eliminar noticia?')">Eliminar</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400">No hay noticias publicadas.</div>
        @endforelse
        @if($noticias instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div>{{ $noticias->links() }}</div>
        @endif
    </div>
</x-panel>
