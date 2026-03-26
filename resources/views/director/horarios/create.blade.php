<x-panel title="Asignar Horario" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    <div class="mb-5">
        <a href="{{ route('director.horarios.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver a Horarios
        </a>
    </div>

    <div class="max-w-3xl bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-[15px] font-semibold text-gray-800 mb-1">Asignar Horario Semanal</h3>
        <p class="text-[12px] text-gray-400 mb-5">Selecciona los días y horarios para una materia, grupo y docente.</p>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm mb-5">
                <ul class="list-disc list-inside">@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('director.horarios.store') }}" class="space-y-5">
            @csrf

            {{-- Grupo, Materia, Docente --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Grupo *</label>
                    <select name="id_grupo" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                        <option value="">Seleccionar...</option>
                        @foreach($grupos as $g)
                            <option value="{{ $g->id_grupo }}" {{ old('id_grupo') == $g->id_grupo ? 'selected' : '' }}>{{ $g->clave_grupo }} ({{ $g->carrera?->nombre_carrera ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Materia *</label>
                    <select name="id_materia" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                        <option value="">Seleccionar...</option>
                        @foreach($materias as $m)
                            <option value="{{ $m->id_materia }}" {{ old('id_materia') == $m->id_materia ? 'selected' : '' }}>{{ $m->nombre_materia }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Docente *</label>
                    <select name="id_docente" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                        <option value="">Seleccionar...</option>
                        @foreach($docentes as $doc)
                            <option value="{{ $doc->id_docente }}" {{ old('id_docente') == $doc->id_docente ? 'selected' : '' }}>{{ $doc->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Días de la semana --}}
            <div>
                <label class="block text-xs text-gray-500 mb-3">Días y horarios *</label>
                <div class="space-y-2">
                    @foreach(['lunes' => 'Lunes', 'martes' => 'Martes', 'miercoles' => 'Miércoles', 'jueves' => 'Jueves', 'viernes' => 'Viernes', 'sabado' => 'Sábado'] as $key => $label)
                        <div class="flex items-center gap-4 p-3 rounded-xl border border-gray-100 hover:border-gray-200 transition-colors dia-row">
                            <label class="flex items-center gap-2.5 w-28 flex-shrink-0 cursor-pointer">
                                <input type="checkbox" name="dias[{{ $key }}][activo]" value="1"
                                       class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500/20 dia-check"
                                       data-dia="{{ $key }}"
                                       {{ old("dias.{$key}.activo") ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                            </label>
                            <div class="flex items-center gap-3 flex-1 dia-times {{ old("dias.{$key}.activo") ? '' : 'opacity-40 pointer-events-none' }}" id="times-{{ $key }}">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-400">De</span>
                                    <input type="time" name="dias[{{ $key }}][hora_inicio]"
                                           value="{{ old("dias.{$key}.hora_inicio", '07:00') }}"
                                           class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-400">A</span>
                                    <input type="time" name="dias[{{ $key }}][hora_fin]"
                                           value="{{ old("dias.{$key}.hora_fin", '08:00') }}"
                                           class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="text-[11px] text-gray-400 mt-2">Marca al menos un día. Puedes asignar horarios diferentes para cada día.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-colors">Asignar Horario</button>
                <a href="{{ route('director.horarios.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors">Cancelar</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('.dia-check').forEach(cb => {
            cb.addEventListener('change', function() {
                const times = document.getElementById('times-' + this.dataset.dia);
                if (this.checked) {
                    times.classList.remove('opacity-40', 'pointer-events-none');
                } else {
                    times.classList.add('opacity-40', 'pointer-events-none');
                }
            });
        });
    </script>
    @endpush
</x-panel>
