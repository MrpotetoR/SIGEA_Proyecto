<x-panel title="Noticias" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    {{-- Filtro por fecha --}}
    <form method="GET" class="flex gap-3 mb-6 items-center">
        <label class="text-sm font-medium text-gray-600 dark:text-gray-300">Desde:</label>
        <input type="date" name="desde" value="{{ request('desde') }}"
               class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        <button type="submit"
                class="bg-[#0606F0] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#04276B] transition-colors">
            Filtrar
        </button>
        @if(request('desde'))
            <a href="{{ route('alumno.noticias') }}"
               class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 underline">Limpiar</a>
        @endif
    </form>

    @if($noticias->isNotEmpty())
        <div class="space-y-4" x-data="{ lightbox: null }">
            @foreach($noticias as $noticia)
                <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-6">
                    <div class="flex items-start gap-4">
                        @if($noticia->imagen_url)
                            <img src="{{ $noticia->imagen_url }}" alt=""
                                 @click="lightbox = '{{ $noticia->imagen_url }}'"
                                 class="w-20 h-20 rounded-lg object-cover flex-shrink-0 border dark:border-gray-700 cursor-zoom-in hover:opacity-80 transition-opacity">
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-base mb-1">{{ $noticia->titulo }}</h3>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">
                                <span class="inline-flex items-center gap-1"><x-icon name="calendar" class="w-3.5 h-3.5" /> {{ \Carbon\Carbon::parse($noticia->fecha_publicacion)->format('d/m/Y H:i') }}</span>
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                {{ Str::limit($noticia->contenido, 250) }}
                            </p>
                            @include('partials.noticia-audiencia', ['noticia' => $noticia])
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
    @else
        <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-12 text-center text-gray-400 dark:text-gray-500">
            Sin noticias disponibles.
        </div>
    @endif

</x-panel>
