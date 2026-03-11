<x-panel title="Subir Documento" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>
    <div class="max-w-lg">
        <a href="{{ route('servicios.documentos.index') }}" class="text-sm text-indigo-600 hover:underline mb-6 inline-block">← Volver</a>
        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ route('servicios.documentos.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                    <input type="text" name="titulo" value="{{ old('titulo') }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none @error('titulo') border-red-400 @enderror">
                    @error('titulo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                    <input type="text" name="tipo" value="{{ old('tipo') }}" required
                           placeholder="Ej: Reglamento, Convocatoria, Circular"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none @error('tipo') border-red-400 @enderror">
                    @error('tipo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de publicación *</label>
                    <input type="date" name="fecha_publicacion" value="{{ old('fecha_publicacion', today()->toDateString()) }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Archivo (PDF/DOC, máx 10MB) *</label>
                    <input type="file" name="archivo" required accept=".pdf,.doc,.docx"
                           class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('archivo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="bg-indigo-700 hover:bg-indigo-800 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">Subir documento</button>
                    <a href="{{ route('servicios.documentos.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
