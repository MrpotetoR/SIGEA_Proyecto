@php
    $user = auth()->user();
    [$panelNombre, $navPartial, $indexRoute] = match(true) {
        $user->hasRole('alumno')            => ['Panel Alumno',      'partials.alumno-nav',    'alumno.noticias'],
        $user->hasRole('docente')           => ['Panel Docente',     'partials.docente-nav',   'docente.noticias'],
        $user->hasRole('director_carrera')  => ['Panel Director',    'partials.director-nav',  'director.noticias'],
        $user->hasRole('servicios_escolares') => ['Servicios Escolares', 'partials.servicios-nav', 'servicios.noticias.index'],
        default                             => ['SIGEA', null, null],
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

            @if($user->hasRole('servicios_escolares'))
                <div class="flex gap-3 pt-5 mt-5 border-t dark:border-gray-700">
                    <a href="{{ route('servicios.noticias.edit', $noticia) }}"
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
