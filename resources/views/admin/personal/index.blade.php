<x-panel title="Gestores Escolares" panelNombre="Panel Admin">
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
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                   placeholder="Nombre, apellido o correo..."
                   class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-blue-400 focus:outline-none">
        </div>
        <button type="submit"
                class="bg-[#0606F0] hover:bg-[#04276B] text-white px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors">Filtrar</button>
        <a href="{{ route('admin.personal.historial') }}"
           class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Historial
        </a>
        <a href="{{ route('admin.personal.create') }}"
           class="sm:ml-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap w-full sm:w-auto text-center">
            + Nuevo personal
        </a>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 overflow-hidden">
        <div class="overflow-y-auto custom-scrollbar" style="max-height: calc(100vh - 220px);">
        <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Correo</th>
                    <th class="px-4 py-3 text-left">Especialidad</th>
                    <th class="px-4 py-3 text-left">Carreras asignadas</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($personal as $p)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                            <div class="flex items-center gap-2">
                                <span>{{ $p->nombre_completo }}</span>
                                @if($p->puede_asignar_carreras)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-semibold uppercase tracking-wide bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-700"
                                          title="Puede asignar carreras a otros gestores">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Permiso especial
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $p->user?->email }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $p->especialidad }}</td>
                        <td class="px-4 py-3">
                            @if($p->carreras->isNotEmpty())
                                <div class="flex flex-wrap gap-1">
                                    @foreach($p->carreras as $c)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">{{ $c->clave_carrera }}</span>
                                    @endforeach
                                </div>
                                <span class="text-[10px] text-gray-400 mt-1 inline-block">{{ $p->carreras->count() }} / {{ \App\Models\GestorEscolar::MAX_CARRERAS }}</span>
                            @else
                                <span class="text-xs text-gray-400">Sin carreras</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-3">
                                <a href="{{ route('admin.personal.show', $p) }}"
                                   class="text-[#0606F0] dark:text-blue-400 hover:text-blue-900 font-medium">Ver</a>
                                <a href="{{ route('admin.personal.edit', $p) }}"
                                   class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 font-medium">Editar</a>
                                <form method="POST" action="{{ route('admin.personal.destroy', $p) }}" class="inline"
                                      data-udea-confirm
                                      data-confirm-title="Eliminar personal"
                                      data-confirm-message="¿Eliminar a <strong>{{ $p->nombre_completo }}</strong>?"
                                      data-confirm-detail="Sus carreras asignadas quedarán sin asignar."
                                      data-confirm-variant="danger"
                                      data-confirm-icon="trash"
                                      data-confirm-button="Eliminar"
                                      data-confirm-cancel="Cancelar">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 font-medium">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">No hay personal registrado.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($personal instanceof \Illuminate\Pagination\LengthAwarePaginator && $personal->hasPages())
            <div class="px-4 py-3 border-t dark:border-gray-700">{{ $personal->links() }}</div>
        @endif
    </div>
</x-panel>
