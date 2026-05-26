<x-panel title="Historial — Caja Chica" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('admin.caja-chica.fondo.edit') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline">
            ← Volver al Fondo
        </a>
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Registro completo de movimientos del fondo, vales y permisos.
        </p>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm dark:shadow-gray-900/20 border dark:border-gray-700 p-4 mb-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1">Acción</label>
                <select name="accion" class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                    <option value="">Todas</option>
                    @foreach(\App\Models\CajaChicaLog::ACCIONES as $k => $v)
                        <option value="{{ $k }}" @selected(request('accion') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1">Motivo</label>
                <select name="motivo" class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                    <option value="">Todos</option>
                    @foreach(\App\Models\CajaChicaLog::MOTIVOS as $k => $v)
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
                <label class="block text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1">Folio del vale</label>
                <input type="text" name="vale_folio" value="{{ request('vale_folio') }}" placeholder="VCC-2026-..."
                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
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
            <div class="flex items-end">
                <label class="text-xs flex items-center gap-2 cursor-pointer text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="con_evidencia" value="1" @checked(request('con_evidencia') === '1')
                           class="rounded">
                    Solo con evidencia
                </label>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="bg-[#0606F0] hover:bg-[#04276B] text-white text-xs font-medium px-4 py-1.5 rounded-lg transition-colors flex-1">
                    Filtrar
                </button>
                <a href="{{ route('admin.caja-chica.historial') }}"
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
                        <th class="px-4 py-3 text-left">Vale / Fondo</th>
                        <th class="px-4 py-3 text-left">Monto</th>
                        <th class="px-4 py-3 text-left">Motivo</th>
                        <th class="px-4 py-3 text-center">Evidencia</th>
                        <th class="px-4 py-3 text-left">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($logs as $log)
                        @php
                            // Color del badge según la acción
                            $colorAccion = match(true) {
                                in_array($log->accion, ['otorgar_permiso', 'autorizar_vale', 'subir_factura', 'cerrar_vale'])
                                    => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                                in_array($log->accion, ['revocar_permiso', 'rechazar_vale', 'cancelar_vale'])
                                    => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                                in_array($log->accion, ['configurar_tope', 'configurar_umbrales', 'editar_vale'])
                                    => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                                in_array($log->accion, ['crear_vale', 'reponer_fondo'])
                                    => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                                default
                                    => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600 dark:text-gray-400">
                                {{ $log->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                {{ $log->usuario?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $colorAccion }}">
                                    {{ $log->accion_legible }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if($log->vale_id && $log->vale)
                                    <a href="{{ route('gestor.caja-chica.show', $log->vale) }}"
                                       class="font-mono font-bold text-[#0606F0] dark:text-blue-400 hover:underline">
                                        {{ $log->vale->folio }}
                                    </a>
                                @elseif($log->fondo_id)
                                    <span class="text-gray-700 dark:text-gray-300">
                                        Fondo {{ $log->created_at->translatedFormat('F Y') }}
                                    </span>
                                @elseif($log->gestorAfectado)
                                    <span class="text-gray-700 dark:text-gray-300">
                                        👤 {{ $log->gestorAfectado->nombre_completo }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                {{ $log->monto_legible }}
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs max-w-xs">
                                {{ $log->motivo_legible }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($log->evidencia_path)
                                    <a href="{{ $log->evidencia_url }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs text-[#0606F0] dark:text-blue-400 hover:underline">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                        Ver
                                    </a>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-[10px] text-gray-400">
                                {{ $log->ip ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-400">
                                No hay movimientos que coincidan con los filtros.
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
