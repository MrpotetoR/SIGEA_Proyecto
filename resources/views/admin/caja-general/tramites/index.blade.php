<x-panel title="Cobros de trámites" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    <div class="space-y-4">

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-center justify-between flex-wrap gap-3">
            <a href="{{ route('admin.caja-general.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline">← Volver al dashboard</a>
            <a href="{{ route('admin.caja-general.cobro-tramite.create') }}"
               class="bg-[#0606F0] hover:bg-[#04276B] text-white px-5 py-2 rounded-lg text-sm font-semibold">
                + Nuevo cobro
            </a>
        </div>

        <form method="GET" class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-4 border border-transparent dark:border-gray-700 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1 block">Buscar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Folio o alumno..."
                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-1.5 text-xs">
            </div>
            <div>
                <label class="text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1 block">Tipo</label>
                <select name="tipo" class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                    <option value="">Todos</option>
                    @foreach(\App\Models\CobroTramite::TIPOS_TRAMITE as $k => $v)
                        <option value="{{ $k }}" @selected(request('tipo') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1 block">Estatus</label>
                <select name="estatus" class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                    <option value="">Todos</option>
                    <option value="cobrado"   @selected(request('estatus') === 'cobrado')>Cobrado</option>
                    <option value="cancelado" @selected(request('estatus') === 'cancelado')>Cancelado</option>
                </select>
            </div>
            <button class="bg-gray-100 dark:bg-gray-700 px-4 py-1.5 rounded-lg text-xs text-gray-700 dark:text-gray-200">Filtrar</button>
        </form>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 overflow-hidden">
            @if($cobros->isEmpty())
                <div class="p-12 text-center text-gray-500 dark:text-gray-400 text-sm">
                    No hay cobros registrados.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/40 border-b dark:border-gray-700">
                            <tr>
                                <th class="text-left px-4 py-3">Folio</th>
                                <th class="text-left px-4 py-3">Fecha</th>
                                <th class="text-left px-4 py-3">Trámite</th>
                                <th class="text-left px-4 py-3">Alumno</th>
                                <th class="text-right px-4 py-3">Monto</th>
                                <th class="text-left px-4 py-3">Método</th>
                                <th class="text-center px-4 py-3">Estatus</th>
                                <th class="text-left px-4 py-3">Cobrado por</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach($cobros as $c)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                    <td class="px-4 py-3 font-mono text-xs text-[#0606F0] dark:text-blue-400">{{ $c->folio }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $c->cobrado_en?->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-700 dark:text-gray-300">{{ $c->concepto_legible }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-700 dark:text-gray-300">
                                        {{ $c->alumno?->nombre_completo ?? '—' }}
                                        <span class="text-[10px] font-mono text-gray-400 ml-1">{{ $c->alumno?->id_alumno_publico }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-semibold text-gray-900 dark:text-gray-100">${{ number_format((float) $c->monto, 2) }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 capitalize">{{ $c->metodo_pago }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($c->esta_cancelado)
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Cancelado</span>
                                        @else
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Cobrado</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">{{ $c->cobrador?->name ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($cobros->hasPages())
                    <div class="p-4 border-t dark:border-gray-700">{{ $cobros->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</x-panel>
