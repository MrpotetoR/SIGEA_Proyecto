<x-panel title="Detalle del Documento" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="max-w-3xl">
        <a href="{{ route('gestor.documentacion-reportes', array_filter(['tab' => 'documentos', 'carpeta' => $documento->carpeta_id])) }}"
            class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">← Volver</a>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-8">

            {{-- Cabecera --}}
            <div class="flex items-start justify-between gap-4 pb-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-14 h-14 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-[#0606F0] dark:text-blue-300 flex-shrink-0">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 truncate">{{ $documento->titulo }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $documento->tipo }}</p>
                    </div>
                </div>
            </div>

            {{-- Datos --}}
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-y-5 gap-x-8 pt-6">
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Fecha de publicación</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-200">
                        {{ optional($documento->fecha_publicacion)->format('d/m/Y') ?? '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Autor</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-200">
                        {{ $documento->autor->name ?? '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Carpeta</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-200">
                        {{ $documento->carpeta?->nombre ?? 'Sin carpeta' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Registrado</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-200">
                        {{ optional($documento->created_at)->format('d/m/Y H:i') ?? '—' }}
                    </dd>
                </div>
            </dl>

            {{-- Acciones --}}
            <div class="flex flex-wrap gap-3 pt-6 mt-6 border-t border-gray-100 dark:border-gray-700">
                @if($documento->archivo_url)
                    <a href="{{ asset('storage/' . $documento->archivo_url) }}" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Ver archivo
                    </a>
                    <a href="{{ asset('storage/' . $documento->archivo_url) }}" download
                        class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Descargar
                    </a>
                @endif
                <a href="{{ route('gestor.documentos.edit', $documento) }}"
                    class="inline-flex items-center gap-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Editar
                </a>
            </div>
        </div>
    </div>
</x-panel>
