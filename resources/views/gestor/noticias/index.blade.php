<x-panel title="Noticias" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="flex justify-end mb-6">
        <a href="{{ route('gestor.noticias.create') }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            + Publicar noticia
        </a>
    </div>

    <div class="space-y-4" x-data="{ eliminar: null, lightbox: null }">
        @forelse($noticias as $n)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-5 flex items-start justify-between gap-4">
                <a href="{{ route('gestor.noticias.show', $n) }}" class="flex-1 min-w-0 group flex gap-4">
                    @if($n->imagen_url)
                        <img src="{{ $n->imagen_url }}" alt=""
                             @click.prevent="lightbox = '{{ $n->imagen_url }}'"
                             class="w-20 h-20 rounded-lg object-cover flex-shrink-0 border dark:border-gray-700 cursor-zoom-in hover:opacity-80 transition-opacity">
                    @endif
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $n->titulo }}</h3>
                            @if($n->fecha_publicacion && $n->fecha_publicacion->isFuture())
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase tracking-wide bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800/60 flex-shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Pendiente
                                </span>
                            @endif
                            @if($n->pdf_url)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase tracking-wide bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800/60 flex-shrink-0" title="Incluye PDF adjunto">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"/>
                                    </svg>
                                    PDF
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mt-1">{{ Str::limit($n->contenido, 120) }}</p>
                        @include('partials.noticia-audiencia', ['noticia' => $n])
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                            @if($n->fecha_publicacion && $n->fecha_publicacion->isFuture())
                                Programada: {{ $n->fecha_publicacion->format('d/m/Y H:i') }}
                            @else
                                {{ $n->fecha_publicacion->format('d/m/Y') }}
                            @endif
                            &middot; {{ $n->autor?->name }}
                        </p>
                    </div>
                </a>
                <div class="flex gap-2 flex-shrink-0">
                    <a href="{{ route('gestor.noticias.edit', $n) }}"
                       class="bg-yellow-100 dark:bg-yellow-900/30 hover:bg-yellow-200 dark:hover:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">Editar</a>
                    <button type="button"
                            @click="eliminar = { id: {{ $n->id_noticia }}, titulo: '{{ addslashes($n->titulo) }}' }"
                            class="bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 text-red-800 dark:text-red-300 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">Eliminar</button>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-12 text-center text-gray-400 dark:text-gray-400">No hay noticias publicadas.</div>
        @endforelse
        @if($noticias instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div>{{ $noticias->links() }}</div>
        @endif

        {{-- Lightbox --}}
        <template x-teleport="body">
            <div x-show="lightbox" x-transition.opacity @click="lightbox = null" @keydown.escape.window="lightbox = null"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 cursor-zoom-out" style="display:none">
                <img :src="lightbox" alt="" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl" @click.stop>
                <button @click="lightbox = null"
                        class="absolute top-4 right-4 w-10 h-10 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center text-xl transition-colors">&times;</button>
            </div>
        </template>

        {{-- Modal de confirmacion --}}
        <template x-teleport="body">
            <div x-show="eliminar" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
                <div class="absolute inset-0 bg-black/50" @click="eliminar = null"></div>
                <div x-show="eliminar" x-transition.scale.95 @click.away="eliminar = null"
                     class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl dark:shadow-gray-900/40 max-w-sm w-full p-6 text-center">
                    <div class="mx-auto w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Eliminar noticia</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Esta accion no se puede deshacer.</p>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-6" x-text="eliminar?.titulo"></p>
                    <div class="flex gap-3 justify-center">
                        <button @click="eliminar = null"
                                class="px-5 py-2 rounded-lg text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Cancelar</button>
                        <form method="POST" :action="'/servicios/noticias/' + eliminar?.id" x-ref="deleteForm">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="px-5 py-2 rounded-lg text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition-colors">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-panel>
