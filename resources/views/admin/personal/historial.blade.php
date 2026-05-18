<x-panel title="Historial de asignaciones" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('admin.personal.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline">
            ← Volver a Gestores Escolares
        </a>
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Registro completo de asignaciones, reasignaciones y desasignaciones de carreras.
        </p>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm dark:shadow-gray-900/20 border dark:border-gray-700 p-4 mb-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1">Acción</label>
                <select name="accion" class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                    <option value="">Todas</option>
                    @foreach(\App\Models\AsignacionCarreraLog::ACCIONES as $k => $v)
                        <option value="{{ $k }}" @selected(request('accion') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1">Motivo</label>
                <select name="motivo" class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                    <option value="">Todos</option>
                    @foreach(\App\Models\AsignacionCarreraLog::MOTIVOS as $k => $v)
                        <option value="{{ $k }}" @selected(request('motivo') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1">Ejecutado por</label>
                <select name="user_id" class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                    <option value="">Cualquiera</option>
                    @foreach($usuariosQueActuan as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1">Gestor afectado</label>
                <select name="gestor_id" class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                    <option value="">Cualquiera</option>
                    @foreach($gestores as $g)
                        <option value="{{ $g->id_personal }}" @selected(request('gestor_id') == $g->id_personal)>{{ $g->nombre_completo }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1">Carrera</label>
                <select name="carrera_id" class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                    <option value="">Cualquiera</option>
                    @foreach($carreras as $c)
                        <option value="{{ $c->id_carrera }}" @selected(request('carrera_id') == $c->id_carrera)>
                            {{ $c->clave_carrera }} — {{ $c->nombre_carrera }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1">Desde</label>
                <input type="date" name="desde" value="{{ request('desde') }}"
                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
            </div>
            <div>
                <label class="block text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1">Hasta</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}"
                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="bg-[#0606F0] hover:bg-[#04276B] text-white text-xs font-medium px-4 py-1.5 rounded-lg transition-colors flex-1">
                    Filtrar
                </button>
                <a href="{{ route('admin.personal.historial') }}"
                   class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium px-4 py-1.5 rounded-lg transition-colors">
                    Limpiar
                </a>
            </div>
        </div>
    </form>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-[11px] uppercase text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Ejecutado por</th>
                        <th class="px-4 py-3 text-left">Acción</th>
                        <th class="px-4 py-3 text-left">Carrera</th>
                        <th class="px-4 py-3 text-left">Gestor afectado</th>
                        <th class="px-4 py-3 text-left">Motivo</th>
                        <th class="px-4 py-3 text-left">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600 dark:text-gray-400">
                                {{ $log->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                {{ $log->usuario?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $colorAccion = match($log->accion) {
                                        'asignar'    => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                                        'reasignar'  => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                                        'desasignar' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                                        default      => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
                                    };
                                @endphp
                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $colorAccion }}">
                                    {{ $log->accion_legible }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-xs font-bold text-blue-700 dark:text-blue-400">
                                    {{ $log->carrera?->clave_carrera ?? '—' }}
                                </span>
                                <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $log->carrera?->nombre_carrera }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                {{ $log->gestorAfectado?->nombre_completo ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs">
                                {{ $log->motivo_legible }}
                            </td>
                            <td class="px-4 py-3 font-mono text-[10px] text-gray-400">
                                {{ $log->ip ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                                No hay registros que coincidan con los filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-4 py-3 border-t dark:border-gray-700">{{ $logs->links() }}</div>
        @endif
    </div>
</x-panel>
