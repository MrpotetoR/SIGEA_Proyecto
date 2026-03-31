<x-panel title="Inscripciones" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Formulario nueva inscripción --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-[15px] font-semibold text-gray-800 dark:text-gray-200 mb-1">Nueva inscripción</h3>
            <p class="text-[11px] text-gray-400 dark:text-gray-400 mb-4">Filtra por carrera y ciclo para encontrar más rápido.</p>

            <form method="POST" action="{{ route('servicios.inscripciones.store') }}" id="form-inscripcion" class="space-y-4">
                @csrf

                {{-- Filtro carrera --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-1.5">Carrera</label>
                    <select id="filtro-carrera" class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                        <option value="">Todas las carreras</option>
                        @foreach($carreras as $c)
                            <option value="{{ $c->id_carrera }}">{{ $c->clave_carrera }} — {{ $c->nombre_carrera }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro ciclo --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-1.5">Ciclo escolar</label>
                    <select id="filtro-ciclo" class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                        <option value="">Todos los ciclos</option>
                        @foreach($ciclos as $ci)
                            <option value="{{ $ci->id_ciclo }}" {{ $ci->id_ciclo == ($ciclos->first()?->id_ciclo) ? 'selected' : '' }}>{{ $ci->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Búsqueda alumno (AJAX + debounce) --}}
                <x-ajax-select
                    name="id_alumno"
                    :url="route('ajax.alumnos')"
                    label="Alumno *"
                    placeholder="Nombre o matrícula..."
                    :required="true"
                />

                {{-- Grupo --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-1.5">Grupo *</label>
                    <select name="id_grupo" id="select-grupo" required
                            class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                        <option value="">Cargando grupos...</option>
                    </select>
                </div>

                <button type="submit"
                        class="w-full bg-gray-900 hover:bg-gray-800 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white py-2.5 rounded-xl text-sm font-semibold transition-colors">
                    Inscribir alumno
                </button>
            </form>
        </div>

        {{-- Lista de inscripciones --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Filtros --}}
            <form method="GET" action="{{ route('servicios.inscripciones') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-4">
                <div class="flex items-end gap-3 flex-wrap">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-1.5">Buscar alumno</label>
                        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Matrícula o nombre..."
                               class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                    </div>
                    <div class="min-w-[130px]">
                        <label class="block text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-1.5">Grupo</label>
                        <select name="grupo" class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                            <option value="">Todos</option>
                            @foreach($grupos as $g)
                                <option value="{{ $g->id_grupo }}" {{ request('grupo') == $g->id_grupo ? 'selected' : '' }}>{{ $g->clave_grupo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[140px]">
                        <label class="block text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-1.5">Carrera</label>
                        <select name="carrera" class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                            <option value="">Todas</option>
                            @foreach($carreras as $c)
                                <option value="{{ $c->id_carrera }}" {{ request('carrera') == $c->id_carrera ? 'selected' : '' }}>{{ $c->clave_carrera }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[120px]">
                        <label class="block text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-1.5">Ciclo</label>
                        <select name="ciclo" class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                            <option value="">Todos</option>
                            @foreach($ciclos as $ci)
                                <option value="{{ $ci->id_ciclo }}" {{ request('ciclo') == $ci->id_ciclo ? 'selected' : '' }}>{{ $ci->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-gray-900 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-colors">Filtrar</button>
                        @if(request()->hasAny(['buscar', 'grupo', 'carrera', 'ciclo']))
                            <a href="{{ route('servicios.inscripciones') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors">Limpiar</a>
                        @endif
                    </div>
                </div>
            </form>

            {{-- Tabla --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-[12px] text-gray-400 dark:text-gray-400">{{ $inscripciones->total() }} inscripción(es)</span>
                </div>
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alumno</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Carrera</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grupo</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ciclo</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @forelse($inscripciones as $i)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-5 py-3">
                                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ $i->alumno?->nombre_completo }}</p>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-400 font-mono">{{ $i->alumno?->matricula }}</p>
                                </td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-400 text-[13px]">{{ $i->alumno?->carrera?->clave_carrera }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-lg">{{ $i->grupo?->clave_grupo }}</span>
                                </td>
                                <td class="px-5 py-3 text-center text-gray-600 dark:text-gray-400 text-[13px]">{{ $i->grupo?->cicloEscolar?->nombre }}</td>
                                <td class="px-5 py-3 text-center text-gray-500 dark:text-gray-400 text-[13px]">{{ $i->fecha_inscripcion?->format('d/m/Y') }}</td>
                                <td class="px-5 py-3 text-center">
                                    <form method="POST" action="{{ route('servicios.inscripciones.destroy', $i) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors"
                                                onclick="return confirm('¿Eliminar inscripción?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-gray-400 dark:text-gray-400 text-sm">
                                    @if(request()->hasAny(['buscar', 'grupo', 'carrera', 'ciclo']))
                                        No se encontraron inscripciones con los filtros seleccionados.
                                    @else
                                        No hay inscripciones registradas.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($inscripciones->hasPages())
                    <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $inscripciones->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const grupoUrl     = '{{ parse_url(route("ajax.grupos"), PHP_URL_PATH) }}';
        const filtroCarrera = document.getElementById('filtro-carrera');
        const filtroCiclo   = document.getElementById('filtro-ciclo');
        const selectGrupo   = document.getElementById('select-grupo');

        async function fetchGrupos() {
            const params = new URLSearchParams();
            if (filtroCarrera.value) params.append('carrera', filtroCarrera.value);
            if (filtroCiclo.value)   params.append('ciclo', filtroCiclo.value);
            selectGrupo.innerHTML = '<option value="">Cargando...</option>';
            try {
                const data = await (await fetch(`${grupoUrl}?${params}`)).json();
                selectGrupo.innerHTML = '<option value="">Seleccionar grupo...</option>';
                data.forEach(g => { const o = new Option(g.texto, g.id); selectGrupo.add(o); });
                if (!data.length) selectGrupo.innerHTML = '<option value="">No hay grupos</option>';
            } catch { selectGrupo.innerHTML = '<option value="">Error</option>'; }
        }

        filtroCarrera.addEventListener('change', fetchGrupos);
        filtroCiclo.addEventListener('change', fetchGrupos);
        fetchGrupos();
    </script>
    @endpush
</x-panel>
