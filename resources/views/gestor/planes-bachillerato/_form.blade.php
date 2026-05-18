@php
    $clave        = old('clave_plan', $plan?->clave_plan);
    $nombre       = old('nombre_plan', $plan?->nombre_plan);
    $semestres    = old('num_semestres', $plan?->num_semestres ?? 6);
    $tipoPeriodo  = old('tipo_periodo', $plan?->tipo_periodo ?? 'semestre');
    $vigente      = old('vigente', $plan?->vigente ?? true);
    $descripcion  = old('descripcion', $plan?->descripcion);
@endphp

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Modalidad *</label>
    <div class="grid grid-cols-2 gap-3">
        <label class="border rounded-lg px-4 py-3 cursor-pointer transition-colors flex items-start gap-3 has-[:checked]:bg-amber-50 dark:has-[:checked]:bg-amber-900/20 has-[:checked]:border-amber-500 dark:border-gray-600 dark:bg-gray-700/40">
            <input type="radio" name="tipo_periodo" value="semestre" @checked($tipoPeriodo === 'semestre')
                   class="mt-1 w-4 h-4 text-amber-500 focus:ring-amber-400">
            <span>
                <span class="block text-sm font-semibold text-gray-800 dark:text-gray-200">Escolarizado</span>
                <span class="block text-[11px] text-gray-500 dark:text-gray-400">Periodos semestrales (6 meses c/u)</span>
            </span>
        </label>
        <label class="border rounded-lg px-4 py-3 cursor-pointer transition-colors flex items-start gap-3 has-[:checked]:bg-amber-50 dark:has-[:checked]:bg-amber-900/20 has-[:checked]:border-amber-500 dark:border-gray-600 dark:bg-gray-700/40">
            <input type="radio" name="tipo_periodo" value="cuatrimestre" @checked($tipoPeriodo === 'cuatrimestre')
                   class="mt-1 w-4 h-4 text-amber-500 focus:ring-amber-400">
            <span>
                <span class="block text-sm font-semibold text-gray-800 dark:text-gray-200">No Escolarizado</span>
                <span class="block text-[11px] text-gray-500 dark:text-gray-400">Periodos cuatrimestrales (4 meses c/u)</span>
            </span>
        </label>
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Clave *</label>
        <input type="text" name="clave_plan" value="{{ $clave }}" maxlength="20" required
               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:outline-none uppercase @error('clave_plan') border-red-400 @enderror"
               placeholder="BGE-2026 / BNE-2026">
        @error('clave_plan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Numero de periodos *</label>
        <select name="num_semestres" required
                class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:outline-none">
            @foreach([3, 4, 5, 6] as $opt)
                <option value="{{ $opt }}" @selected($semestres == $opt)>{{ $opt }}</option>
            @endforeach
        </select>
        <p class="text-[10px] text-gray-400 mt-1">Escolarizado: 6 semestres (3 anios). No Escolarizado: 4 cuatrimestres (18 meses).</p>
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre del plan *</label>
    <input type="text" name="nombre_plan" value="{{ $nombre }}" maxlength="150" required
           class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:outline-none @error('nombre_plan') border-red-400 @enderror"
           placeholder="Bachillerato General Escolarizado">
    @error('nombre_plan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripcion</label>
    <textarea name="descripcion" rows="3" maxlength="1000"
              class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:outline-none">{{ $descripcion }}</textarea>
</div>

<label class="flex items-center gap-2 cursor-pointer">
    <input type="checkbox" name="vigente" value="1" @checked($vigente)
           class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400">
    <span class="text-sm text-gray-700 dark:text-gray-300">Plan vigente</span>
</label>
