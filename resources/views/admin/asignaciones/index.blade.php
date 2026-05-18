<x-panel title="Asignación de carreras" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    <div class="space-y-6">
        {{-- Carreras sin asignar --}}
        @if($carrerasSinAsignar->isNotEmpty())
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-2xl p-5">
                <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-300 mb-3">
                    {{ $carrerasSinAsignar->count() }} carrera(s) sin asignar — usa el formulario de abajo para asignarlas.
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($carrerasSinAsignar as $c)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white dark:bg-gray-800 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-700">
                            {{ $c->nombre_carrera }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Form: asignar carrera a personal --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-4">Asignar nueva carrera</h3>
            <form method="POST" action="{{ route('admin.asignaciones.store') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                @csrf
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Personal de SE</label>
                    <select name="id_personal" required
                            class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <option value="">Seleccionar...</option>
                        @foreach($personal as $p)
                            @php $disponible = $p->carrerasRestantes(); @endphp
                            <option value="{{ $p->id_personal }}" @if($disponible === 0) disabled @endif>
                                {{ $p->nombre_completo }} ({{ $p->carreras->count() }}/{{ \App\Models\GestorEscolar::MAX_CARRERAS }})
                                @if($disponible === 0) — sin cupo @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Carrera</label>
                    <select name="id_carrera" required
                            class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <option value="">Seleccionar...</option>
                        @foreach($carrerasSinAsignar as $c)
                            <option value="{{ $c->id_carrera }}">{{ $c->nombre_carrera }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="bg-[#0606F0] hover:bg-[#04276B] text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors whitespace-nowrap">
                    Asignar
                </button>
            </form>
        </div>

        {{-- Matriz: personal × carreras --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300">Asignaciones actuales</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Personal</th>
                            <th class="px-4 py-3 text-left">Carreras administradas</th>
                            <th class="px-4 py-3 text-center">Cupo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($personal as $p)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100 align-top">
                                    {{ $p->nombre_completo }}
                                    <p class="text-[11px] text-gray-400 font-normal">{{ $p->especialidad }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($p->carreras->isNotEmpty())
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($p->carreras as $c)
                                                <form method="POST" action="{{ route('admin.asignaciones.destroy') }}" class="inline"
                                                      onsubmit="return confirm('¿Desasignar la carrera \'{{ $c->nombre_carrera }}\' de {{ $p->nombre_completo }}? Esta carrera tendrá que ser reasignada a otro personal.');">
                                                    @csrf @method('DELETE')
                                                    <input type="hidden" name="id_personal" value="{{ $p->id_personal }}">
                                                    <input type="hidden" name="id_carrera" value="{{ $c->id_carrera }}">
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 hover:bg-red-100 hover:text-red-700 transition-colors group">
                                                        {{ $c->nombre_carrera }}
                                                        <span class="text-blue-400 group-hover:text-red-500 font-bold">×</span>
                                                    </button>
                                                </form>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">Sin carreras asignadas</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center align-top">
                                    @php $usadas = $p->carreras->count(); $max = \App\Models\GestorEscolar::MAX_CARRERAS; @endphp
                                    <span class="text-xs font-semibold {{ $usadas >= $max ? 'text-red-600' : 'text-gray-600 dark:text-gray-300' }}">
                                        {{ $usadas }} / {{ $max }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-10 text-center text-gray-400">Aún no hay Gestores Escolares registrado. <a href="{{ route('admin.personal.create') }}" class="text-[#0606F0] hover:underline">Crear el primero →</a></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-panel>
