<x-panel title="Administradores" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    <form method="GET" class="flex flex-wrap gap-3 mb-6 items-end bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm dark:shadow-gray-900/20 border border-transparent dark:border-gray-700">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre o correo..."
                   class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-blue-400 focus:outline-none">
        </div>
        <button type="submit"
                class="bg-[#0606F0] hover:bg-[#04276B] text-white px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors">Filtrar</button>
        <a href="{{ route('admin.administradores.create') }}"
           class="sm:ml-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap w-full sm:w-auto text-center">+ Nuevo admin</a>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 overflow-hidden">
        <div class="overflow-y-auto custom-scrollbar" style="max-height: calc(100vh - 220px);">
        <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Correo</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Fecha de registro</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($admins as $a)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                            {{ $a->name }}
                            @if($a->id === auth()->id())
                                <span class="ml-2 text-[10px] font-medium px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Tú</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $a->email }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($a->activo)
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Activo</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ $a->created_at?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-3">
                                <a href="{{ route('admin.administradores.edit', $a) }}"
                                   class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 font-medium">Editar</a>
                                @if($a->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.administradores.destroy', $a) }}" class="inline"
                                          onsubmit="return confirm('¿Eliminar este administrador?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 font-medium">Eliminar</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">No hay administradores registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($admins instanceof \Illuminate\Pagination\LengthAwarePaginator && $admins->hasPages())
            <div class="px-4 py-3 border-t dark:border-gray-700">{{ $admins->links() }}</div>
        @endif
    </div>
</x-panel>
