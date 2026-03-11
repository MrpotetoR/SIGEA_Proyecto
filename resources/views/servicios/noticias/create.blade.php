<x-panel title="Publicar Noticia" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>
    <div class="max-w-2xl">
        <a href="{{ route('servicios.noticias.index') }}" class="text-sm text-indigo-600 hover:underline mb-6 inline-block">← Volver</a>
        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ route('servicios.noticias.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                    <input type="text" name="titulo" value="{{ old('titulo') }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none @error('titulo') border-red-400 @enderror">
                    @error('titulo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contenido *</label>
                    <textarea name="contenido" rows="6" required
                              class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none @error('contenido') border-red-400 @enderror">{{ old('contenido') }}</textarea>
                    @error('contenido')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de publicación *</label>
                        <input type="date" name="fecha_publicacion" value="{{ old('fecha_publicacion', today()->toDateString()) }}" required
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer mb-2">
                            <input type="checkbox" name="activa" value="1" @checked(old('activa', true))
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            Publicar de inmediato
                        </label>
                    </div>
                </div>
                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="bg-indigo-700 hover:bg-indigo-800 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">Publicar</button>
                    <a href="{{ route('servicios.noticias.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
