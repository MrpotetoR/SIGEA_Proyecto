<x-panel title="Subir Documento" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>
    <div class="max-w-lg">
        <a href="{{ route('gestor.documentacion-reportes', array_filter(['tab' => 'documentos', 'carpeta' => $carpetaActual?->id_carpeta])) }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">← Volver</a>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
            @if(isset($carpetaActual) && $carpetaActual)
                <div class="mb-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-3 text-sm text-blue-800 dark:text-blue-200">
                    Subiendo en la carpeta: <strong>{{ $carpetaActual->nombre }}</strong>
                    @if($carpetaActual->esPrivada())
                        <span class="ml-2 text-xs bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 px-2 py-0.5 rounded">Privada</span>
                    @endif
                </div>
            @endif
            <form method="POST" action="{{ route('gestor.documentos.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @if(isset($carpetaActual) && $carpetaActual)
                    <input type="hidden" name="carpeta_id" value="{{ $carpetaActual->id_carpeta }}">
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título *</label>
                    <input type="text" name="titulo" value="{{ old('titulo') }}" required
                           class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('titulo') border-red-400 @enderror">
                    @error('titulo')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo *</label>
                    <input type="text" name="tipo" value="{{ old('tipo') }}" required
                           placeholder="Ej: Reglamento, Convocatoria, Circular"
                           class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('tipo') border-red-400 @enderror">
                    @error('tipo')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de publicación *</label>
                    <input type="date" name="fecha_publicacion" value="{{ old('fecha_publicacion', today()->toDateString()) }}" required
                           class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Archivo (PDF/DOC, máx 10MB) *</label>
                    <input type="file" name="archivo" required accept=".pdf,.doc,.docx"
                           class="w-full text-sm text-gray-600 dark:text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50">
                    @error('archivo')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="submit" class="bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">Subir documento</button>
                    <a href="{{ route('gestor.documentacion-reportes', array_filter(['tab' => 'documentos', 'carpeta' => $carpetaActual?->id_carpeta])) }}" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
