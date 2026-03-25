<x-panel title="Directores de Carrera" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    <form method="GET" class="flex gap-3 mb-6 items-end bg-white p-4 rounded-xl shadow-sm">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Buscar</label>
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                   placeholder="Nombre o apellido..."
                   class="border rounded-lg px-3 py-2 text-sm w-56 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
        </div>
        <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Filtrar
        </button>
        <a href="{{ route('servicios.directores.create') }}"
           class="ml-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            + Nuevo director
        </a>
    </form>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Correo</th>
                    <th class="px-4 py-3 text-left">Especialidad</th>
                    <th class="px-4 py-3 text-left">Carrera asignada</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($directores as $d)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $d->nombre_completo }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $d->user?->email }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $d->especialidad ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            @if($d->carrerasDirigidas->isNotEmpty())
                                @foreach($d->carrerasDirigidas as $c)
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">{{ $c->nombre_carrera }}</span>
                                @endforeach
                            @else
                                <span class="text-gray-400">Sin asignar</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($d->user?->activo)
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Activo</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('servicios.directores.show', $d) }}"
                                   class="text-indigo-600 hover:text-indigo-900 font-medium">Ver</a>
                                <a href="{{ route('servicios.directores.edit', $d) }}"
                                   class="text-yellow-600 hover:text-yellow-900 font-medium">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">No hay directores registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($directores instanceof \Illuminate\Pagination\LengthAwarePaginator && $directores->hasPages())
            <div class="px-4 py-3 border-t">{{ $directores->links() }}</div>
        @endif
    </div>
</x-panel>
