@php
    $user = auth()->user();
    [$panelNombre, $navPartial, $indexRoute] = match(true) {
        $user->hasRole('alumno')            => ['Panel Alumno',      'partials.alumno-nav',    'alumno.noticias'],
        $user->hasRole('docente')           => ['Panel Docente',     'partials.docente-nav',   'docente.noticias'],
        $user->hasRole('gestor_escolar')    => ['Gestor Escolar',    'partials.gestor-nav',    'gestor.noticias.index'],
        default                             => ['UDEA', null, null],
    };
@endphp

<x-panel title="Detalle de Noticia" :panelNombre="$panelNombre">
    @if($navPartial)
        <x-slot name="nav">@include($navPartial)</x-slot>
    @endif

    <div class="max-w-2xl" x-data="{ lightbox: false }">
        @if($indexRoute)
            <a href="{{ route($indexRoute) }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">&larr; Volver a noticias</a>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ $noticia->titulo }}</h2>

            <div class="flex flex-wrap gap-4 text-xs text-gray-500 dark:text-gray-400 mb-6">
                <span>Publicada: {{ $noticia->fecha_publicacion?->format('d/m/Y H:i') }}</span>
                <span>Autor: {{ $noticia->autor?->name ?? 'Sistema' }}</span>
            </div>

            @if($noticia->imagen_url)
                <div class="mb-5">
                    <img src="{{ $noticia->imagen_url }}" alt="{{ $noticia->titulo }}" @click="lightbox = true"
                         class="w-full max-h-64 object-cover rounded-lg border dark:border-gray-700 cursor-zoom-in hover:opacity-90 transition-opacity">
                </div>
            @endif

            <div class="border-t dark:border-gray-700 pt-5">
                <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $noticia->contenido }}</div>
            </div>

            @if($noticia->pdf_url)
                <div class="mt-5 pt-5 border-t dark:border-gray-700">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 mb-2">Documento adjunto</p>
                    <a href="{{ $noticia->pdf_url }}" target="_blank" download="{{ $noticia->pdf_nombre ?? 'documento' }}.pdf"
                       class="inline-flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/40 hover:border-red-300 dark:hover:border-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors group">
                        <span class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/40 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">
                                {{ $noticia->pdf_nombre ?? 'Documento oficial' }}
                            </span>
                            <span class="block text-[11px] text-gray-500 dark:text-gray-400">PDF — abrir o descargar</span>
                        </span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </a>
                </div>
            @endif

            @if($user->hasRole('gestor_escolar'))
                <div class="flex gap-3 pt-5 mt-5 border-t dark:border-gray-700">
                    <a href="{{ route('gestor.noticias.edit', $noticia) }}"
                       class="bg-blue-700 hover:bg-blue-800 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors">Editar</a>
                </div>
            @endif
        </div>

        @if($noticia->imagen_url)
        <template x-teleport="body">
            <div x-show="lightbox" x-transition.opacity @click="lightbox = false" @keydown.escape.window="lightbox = false"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 cursor-zoom-out" style="display:none">
                <img src="{{ $noticia->imagen_url }}" alt="{{ $noticia->titulo }}"
                     class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl" @click.stop>
                <button @click="lightbox = false"
                        class="absolute top-4 right-4 w-10 h-10 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center text-xl transition-colors">&times;</button>
            </div>
        </template>
        @endif
    </div>
</x-panel>
