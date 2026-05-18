<x-panel title="Nueva Materia" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>
    @php $ringColor = ($esBachi ?? false) ? 'focus:ring-amber-400' : 'focus:ring-blue-400'; @endphp
    @php $btnColor  = ($esBachi ?? false) ? 'bg-amber-500 hover:bg-amber-600' : 'bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400'; @endphp
    <div class="max-w-lg">
        <a href="{{ route('gestor.materias.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">← Volver</a>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <form method="POST" action="{{ route('gestor.materias.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre de la materia *</label>
                    <input type="text" name="nombre_materia" value="{{ old('nombre_materia') }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm {{ $ringColor }} focus:outline-none focus:ring-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 @error('nombre_materia') border-red-400 @enderror">
                    @error('nombre_materia')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                @if($esBachi ?? false)
                    {{-- Bachillerato: Plan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plan de Bachillerato *</label>
                        <select name="id_plan_bachillerato" id="sel-origen-mat" required class="w-full border rounded-lg px-3 py-2 text-sm {{ $ringColor }} focus:outline-none focus:ring-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                            <option value="" data-max="6" data-label="Semestre">Seleccionar...</option>
                            @foreach($planesBachi as $p)
                                <option value="{{ $p->id_plan_bachillerato }}"
                                        data-max="{{ $p->num_semestres }}"
                                        data-label="Semestre"
                                        @selected(old('id_plan_bachillerato') == $p->id_plan_bachillerato)>
                                    {{ $p->nombre_plan }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_plan_bachillerato')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                @else
                    {{-- Universidad: Carrera --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Carrera *</label>
                        <select name="id_carrera" id="sel-origen-mat" required class="w-full border rounded-lg px-3 py-2 text-sm {{ $ringColor }} focus:outline-none focus:ring-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                            <option value="" data-max="10" data-label="Cuatrimestre">Seleccionar...</option>
                            @foreach($carreras as $c)
                                <option value="{{ $c->id_carrera }}"
                                        data-max="{{ $c->max_periodos }}"
                                        data-label="{{ $c->label_periodo }}"
                                        @selected(old('id_carrera') == $c->id_carrera)>{{ $c->nombre_carrera }} ({{ $c->label_periodo }})</option>
                            @endforeach
                        </select>
                        @error('id_carrera')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" id="lbl-cuatri-mat">
                            {{ ($esBachi ?? false) ? 'Semestre' : 'Cuatrimestre' }} *
                        </label>
                        <select name="cuatrimestre" id="sel-cuatri-mat" required class="w-full border rounded-lg px-3 py-2 text-sm {{ $ringColor }} focus:outline-none focus:ring-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                            @for($i = 1; $i <= (($esBachi ?? false) ? 6 : 10); $i++)
                                <option value="{{ $i }}" @selected(old('cuatrimestre') == $i)>{{ $i }}°</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Horas/semana
                            <span class="text-gray-400 font-normal text-xs">(opcional)</span>
                        </label>
                        <input type="number" name="horas_semana" value="{{ old('horas_semana') }}" min="1" max="60"
                               placeholder="Ej: 4"
                               class="w-full border rounded-lg px-3 py-2 text-sm {{ $ringColor }} focus:outline-none focus:ring-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                        <p class="text-[10px] text-gray-400 mt-1">Puedes capturarla después.</p>
                    </div>
                </div>
                <div class="flex gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="submit" class="{{ $btnColor }} text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">Crear materia</button>
                    <a href="{{ route('gestor.materias.index') }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const selC = document.getElementById('sel-origen-mat');
    const selP = document.getElementById('sel-cuatri-mat');
    const lbl  = document.getElementById('lbl-cuatri-mat');
    if (!selC || !selP) return;

    function sync() {
        const opt = selC.options[selC.selectedIndex];
        const max = parseInt(opt?.dataset.max || 10);
        const label = opt?.dataset.label || 'Cuatrimestre';
        const cur = parseInt(selP.value) || 1;
        selP.innerHTML = '';
        for (let i = 1; i <= max; i++) {
            const o = document.createElement('option');
            o.value = i; o.textContent = i + '°';
            if (i === Math.min(cur, max)) o.selected = true;
            selP.appendChild(o);
        }
        if (lbl) lbl.textContent = label + ' *';
    }
    selC.addEventListener('change', sync);
    sync();
});
</script>
