<x-panel title="Documentos Institucionales" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="flex justify-end mb-6">
        <a href="{{ route('servicios.documentos.create') }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            + Subir documento
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3 text-left">Título</th>
                    <th class="px-4 py-3 text-left">Tipo</th>
                    <th class="px-4 py-3 text-center">Fecha</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($documentos as $d)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $d->titulo }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $d->tipo }}</td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ $d->fecha_publicacion?->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $d->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                                {{ $d->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('servicios.documentos.edit', $d) }}"
                                   class="text-yellow-600 hover:text-yellow-900 font-medium">Editar</a>
                                <form method="POST" action="{{ route('servicios.documentos.destroy', $d) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium"
                                            onclick="return confirm('¿Eliminar documento?')">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">No hay documentos publicados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-panel>
