<x-panel title="Docentes" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

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
        <a href="{{ route('servicios.docentes.create') }}"
           class="ml-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            + Nuevo docente
        </a>
    </form>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Correo</th>
                    <th class="px-4 py-3 text-left">Especialidad</th>
                    <th class="px-4 py-3 text-center">Horas contrato</th>
                    <th class="px-4 py-3 text-center">Tutor</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($docentes as $d)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $d->nombre_completo }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $d->user?->email }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $d->especialidad ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">{{ $d->horas_contrato }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($d->es_tutor)
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Sí</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('servicios.docentes.show', $d) }}"
                                   class="text-indigo-600 hover:text-indigo-900 font-medium">Ver</a>
                                <a href="{{ route('servicios.docentes.edit', $d) }}"
                                   class="text-yellow-600 hover:text-yellow-900 font-medium">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">No hay docentes registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($docentes instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="px-4 py-3 border-t">{{ $docentes->links() }}</div>
        @endif
    </div>
</x-panel>
