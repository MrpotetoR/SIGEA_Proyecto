<x-panel title="Nuevo Ciclo Escolar" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>
    <div class="max-w-lg">
        <a href="{{ route('servicios.ciclos.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">← Volver</a>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <div class="mb-5 text-xs text-gray-500 dark:text-gray-400 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-lg px-3 py-2">
                ℹ Un ciclo escolar tiene una duración fija de <strong>3 años y 4 meses</strong>. El nombre y la fecha de fin se generan automáticamente a partir de la fecha de inicio.
            </div>
            <form method="POST" action="{{ route('servicios.ciclos.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de inicio *</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio') }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 @error('fecha_inicio') border-red-400 @enderror">
                    <p id="warn_anio" class="hidden text-red-500 dark:text-red-400 text-xs mt-1"></p>
                    @error('fecha_inicio')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre del ciclo</label>
                        <div id="prev_nombre" class="w-full border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2 text-sm text-gray-600 dark:text-gray-300">—</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de fin</label>
                        <div id="prev_fin" class="w-full border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2 text-sm text-gray-600 dark:text-gray-300">—</div>
                    </div>
                </div>
                <div class="flex gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="submit" id="btn_submit" class="bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">Crear ciclo</button>
                    <a href="{{ route('servicios.ciclos.index') }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
<script>
(function() {
    const inp = document.getElementById('fecha_inicio');
    const prevN = document.getElementById('prev_nombre');
    const prevF = document.getElementById('prev_fin');
    const warn = document.getElementById('warn_anio');
    const btn  = document.getElementById('btn_submit');
    const aniosUsados = @json($aniosUsados);
    function pad(n){ return String(n).padStart(2,'0'); }
    function calc() {
        if (!inp.value) { prevN.textContent = '—'; prevF.textContent = '—'; warn.classList.add('hidden'); btn.disabled = false; btn.classList.remove('opacity-50','cursor-not-allowed'); return; }
        const d = new Date(inp.value + 'T00:00:00');
        if (isNaN(d)) return;
        const fin = new Date(d);
        fin.setFullYear(fin.getFullYear() + 3);
        fin.setMonth(fin.getMonth() + 4);
        prevN.textContent = d.getFullYear() + '–' + fin.getFullYear();
        prevF.textContent = fin.getFullYear() + '-' + pad(fin.getMonth()+1) + '-' + pad(fin.getDate());
        if (aniosUsados.includes(d.getFullYear())) {
            warn.textContent = 'Ya existe un ciclo que inicia en ' + d.getFullYear() + '. Sólo se permite un ciclo por año de inicio.';
            warn.classList.remove('hidden');
            btn.disabled = true; btn.classList.add('opacity-50','cursor-not-allowed');
        } else {
            warn.classList.add('hidden');
            btn.disabled = false; btn.classList.remove('opacity-50','cursor-not-allowed');
        }
    }
    inp.addEventListener('change', calc);
    inp.addEventListener('input', calc);
    calc();
})();
</script>
