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
        <div class="space-y-4">
            @foreach($noticias as $noticia)
                <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-base mb-1">{{ $noticia->titulo }}</h3>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">
                                📅 {{ \Carbon\Carbon::parse($noticia->fecha_publicacion)->format('d/m/Y H:i') }}
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                {{ Str::limit($noticia->contenido, 250) }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        <div class="mt-6">
            {{ $noticias->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-12 text-center text-gray-400 dark:text-gray-500">
            Sin noticias disponibles.
        </div>
    @endif

</x-panel>
