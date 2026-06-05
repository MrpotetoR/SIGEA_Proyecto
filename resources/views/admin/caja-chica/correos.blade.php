<x-panel title="Correos adicionales de notificación" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    <div class="max-w-3xl space-y-6">
        <a href="{{ route('admin.caja-chica.fondo.edit') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline inline-block">← Volver al Fondo</a>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- Info card --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-4 text-sm text-blue-800 dark:text-blue-200">
            <p class="font-semibold mb-1 inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Correos adicionales para notificaciones de Caja Chica
            </p>
            <p class="text-xs leading-relaxed">
                Estos correos recibirán <strong>copia</strong> de las notificaciones críticas que llegan a tu correo
                principal (<strong>{{ auth()->user()->email }}</strong>), como el aviso de reposición pendiente
                cuando faltan 3 días o menos para el cierre de mes.
            </p>
            <p class="text-xs mt-2">
                <strong>Cupos:</strong> {{ $usados }}/{{ $max }} usados ·
                {{ $libres }} disponible{{ $libres === 1 ? '' : 's' }}.
            </p>
        </div>

        {{-- Form agregar --}}
        @if($libres > 0)
            <form method="POST" action="{{ route('admin.caja-chica.correos.store') }}"
                  class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 space-y-4">
                @csrf
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300">Agregar un correo</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correo electrónico *</label>
                        <input type="email" name="email" required maxlength="150"
                               value="{{ old('email') }}"
                               placeholder="contadora@udea.mx"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre <span class="text-gray-400 font-normal">(opcional)</span>
                        </label>
                        <input type="text" name="nombre_destinatario" maxlength="100"
                               value="{{ old('nombre_destinatario') }}"
                               placeholder="Mtra. López"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                        <p class="text-[10px] text-gray-500 mt-0.5">Para personalizar el saludo del correo.</p>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="bg-[#0606F0] hover:bg-[#04276B] text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors">
                        + Agregar correo
                    </button>
                </div>
            </form>
        @else
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-4 text-sm text-amber-800 dark:text-amber-200">
                Ya tienes el máximo de {{ $max }} correos adicionales registrados. Elimina alguno para liberar cupo.
            </div>
        @endif

        {{-- Lista actual --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-3 border-b dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300">
                    Correos registrados ({{ $correos->count() }})
                </h3>
            </div>

            @if($correos->isEmpty())
                <div class="p-12 text-center text-gray-500 dark:text-gray-400 text-sm">
                    Aún no tienes correos adicionales.<br>
                    Agrega hasta {{ $max }} correos para que reciban copia de las notificaciones.
                </div>
            @else
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="text-left px-6 py-3">Correo</th>
                            <th class="text-left px-6 py-3">Destinatario</th>
                            <th class="text-center px-6 py-3">Estado</th>
                            <th class="text-right px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        @foreach($correos as $c)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-6 py-3 text-gray-900 dark:text-gray-100 font-mono text-xs">
                                    {{ $c->email }}
                                </td>
                                <td class="px-6 py-3 text-gray-700 dark:text-gray-300 text-xs">
                                    {{ $c->nombre_destinatario ?? '—' }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    @if($c->activo)
                                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300">
                                            Activo
                                        </span>
                                    @else
                                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                            Pausado
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <form method="POST" action="{{ route('admin.caja-chica.correos.toggle', $c) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="text-xs text-amber-600 dark:text-amber-400 hover:underline">
                                            {{ $c->activo ? 'Pausar' : 'Reactivar' }}
                                        </button>
                                    </form>
                                    <span class="text-gray-300 dark:text-gray-600 mx-1">·</span>
                                    <form method="POST" action="{{ route('admin.caja-chica.correos.destroy', $c) }}" class="inline"
                                          data-udea-confirm
                                          data-confirm-title="Eliminar correo"
                                          data-confirm-message="¿Eliminar <strong>{{ $c->correo }}</strong>?"
                                          data-confirm-detail="Ya no recibirá notificaciones de caja chica."
                                          data-confirm-variant="danger"
                                          data-confirm-icon="trash"
                                          data-confirm-button="Eliminar"
                                          data-confirm-cancel="Cancelar">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-xs text-red-600 dark:text-red-400 hover:underline">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-panel>
