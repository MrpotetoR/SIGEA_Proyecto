<x-panel title="Directores de Carrera" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    <form method="GET" class="flex gap-3 mb-6 items-end bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm dark:shadow-gray-900/20 border border-transparent dark:border-gray-700">
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                   placeholder="Nombre o apellido..."
                   class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm w-56 focus:ring-2 focus:ring-blue-400 focus:outline-none">
        </div>
        <button type="submit"
                class="bg-[#0606F0] hover:bg-[#04276B] dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Filtrar
        </button>
        <a href="{{ route('servicios.directores.create') }}"
           class="ml-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            + Nuevo director
        </a>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 flex flex-col min-h-0" style="max-height: calc(100vh - 220px);">
        <div class="overflow-y-auto flex-1 custom-scrollbar">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Correo</th>
                    <th class="px-4 py-3 text-left">Especialidad</th>
                    <th class="px-4 py-3 text-left">Carrera asignada</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($directores as $d)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $d->nombre_completo }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $d->user?->email }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $d->especialidad ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            @if($d->carrerasDirigidas->isNotEmpty())
                                @foreach($d->carrerasDirigidas as $c)
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">{{ $c->nombre_carrera }}</span>
                                @endforeach
                            @else
                                <span class="text-gray-400 dark:text-gray-400">Sin asignar</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($d->user?->activo)
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Activo</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('servicios.directores.show', $d) }}"
                                   class="text-[#0606F0] dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 font-medium">Ver</a>
                                <a href="{{ route('servicios.directores.edit', $d) }}"
                                   class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 font-medium">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400 dark:text-gray-400">No hay directores registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($directores instanceof \Illuminate\Pagination\LengthAwarePaginator && $directores->hasPages())
            <div class="px-4 py-3 border-t dark:border-gray-700 flex-shrink-0">{{ $directores->links() }}</div>
        @endif
    </div>
</x-panel>
