<x-panel title="Noticias" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Noticias</h1>

    @if($noticias->isNotEmpty())
        <div class="space-y-3">
            @foreach($noticias as $noticia)
                <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#0606F0] dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-[15px] font-bold text-gray-800 dark:text-gray-200">{{ $noticia->titulo }}</h3>
                            <p class="text-[12px] text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $noticia->contenido }}</p>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-2">{{ $noticia->fecha_publicacion->format('d/m/Y') }} &mdash; {{ $noticia->fecha_publicacion->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $noticias->links() }}</div>
    @else
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-[14px] text-gray-400 dark:text-gray-500">Sin noticias disponibles.</p>
        </div>
    @endif

</div>

</x-panel>
