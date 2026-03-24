<x-panel title="Noticias" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    @if($noticias->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            <p class="text-gray-500 text-sm">No hay noticias publicadas.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($noticias as $noticia)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="text-[15px] font-semibold text-gray-800">{{ $noticia->titulo }}</h3>
                        <span class="text-xs text-gray-400 whitespace-nowrap ml-4">{{ $noticia->fecha_publicacion?->format('d/m/Y') }}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ Str::limit($noticia->contenido, 300) }}</p>
                    @if($noticia->autor)
                        <p class="text-xs text-gray-400 mt-3">Publicado por: {{ $noticia->autor->name }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $noticias->links() }}
        </div>
    @endif
</x-panel>
