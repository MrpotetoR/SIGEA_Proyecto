<x-panel title="Noticias" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    @if($noticias->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 text-sm">No hay noticias publicadas.</p>
        </div>
    @else
        <div class="space-y-4" x-data="{ lightbox: null }">
            @foreach($noticias as $noticia)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4">
                        @if($noticia->imagen_url)
                            <img src="{{ $noticia->imagen_url }}" alt=""
                                 @click="lightbox = '{{ $noticia->imagen_url }}'"
                                 class="w-20 h-20 rounded-lg object-cover flex-shrink-0 border dark:border-gray-700 cursor-zoom-in hover:opacity-80 transition-opacity">
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-[15px] font-semibold text-gray-800 dark:text-gray-200">{{ $noticia->titulo }}</h3>
                                <span class="text-xs text-gray-400 whitespace-nowrap ml-4">{{ $noticia->fecha_publicacion?->format('d/m/Y') }}</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ Str::limit($noticia->contenido, 300) }}</p>
                            @include('partials.noticia-audiencia', ['noticia' => $noticia])
                            @if($noticia->autor)
                                <p class="text-xs text-gray-400 mt-3">Publicado por: {{ $noticia->autor->name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Lightbox --}}
            <template x-teleport="body">
                <div x-show="lightbox" x-transition.opacity @click="lightbox = null" @keydown.escape.window="lightbox = null"
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 cursor-zoom-out" style="display:none">
                    <img :src="lightbox" alt="" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl" @click.stop>
                    <button @click="lightbox = null"
                            class="absolute top-4 right-4 w-10 h-10 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center text-xl transition-colors">&times;</button>
                </div>
            </template>
        </div>

        <div class="mt-6">
            {{ $noticias->links() }}
        </div>
    @endif
</x-panel>
