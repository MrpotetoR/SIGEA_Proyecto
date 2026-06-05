<x-panel title="Editar Servicio Social" panelNombre="Panel Gestor Escolar">
<x-slot name="nav">@include('partials.gestor-nav')</x-slot>

<div class="max-w-2xl space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Editar Servicio Social</h1>

    <form method="POST" action="{{ route('gestor.servicio-social.update', $servicioSocial->id_servicio) }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4 dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20">
        @csrf @method('PUT')

        <div>
            <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block dark:text-gray-400">Alumno</label>
            <p class="text-[14px] font-medium text-gray-800 bg-gray-50 rounded-xl px-3 py-2.5 dark:text-gray-200 dark:bg-gray-700/50">{{ $servicioSocial->alumno?->nombre_completo }}</p>
        </div>

        <div>
            <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block dark:text-gray-400">Institución</label>
            <input type="text" name="institucion" value="{{ old('institucion', $servicioSocial->institucion) }}" maxlength="150" pattern="[\p{L}\p{N}\s]+" placeholder="Nombre de la institución donde realiza el servicio" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-sky-300 outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">Solo letras y números.</p>
            @error('institucion') <p class="text-red-500 text-[11px] mt-1 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block dark:text-gray-400">Horas Requeridas</label>
                <input type="number" name="horas_requeridas" value="{{ old('horas_requeridas', (int) $servicioSocial->horas_requeridas) }}" min="0" max="2000" step="1" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-sky-300 outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">
                    Default de carrera: <strong>{{ $servicioSocial->alumno?->carrera?->horas_servicio_social_default ?? 480 }} h</strong>.
                </p>
                @error('horas_requeridas') <p class="text-red-500 text-[11px] mt-1 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block dark:text-gray-400">Horas Acumuladas</label>
                <input type="number" name="horas_acumuladas" value="{{ old('horas_acumuladas', (int) $servicioSocial->horas_acumuladas) }}" min="0" max="2000" step="1" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-sky-300 outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">Horas que el alumno lleva cumplidas.</p>
                @error('horas_acumuladas') <p class="text-red-500 text-[11px] mt-1 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block dark:text-gray-400">Estatus</label>
            <select name="estatus" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-sky-300 outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                <option value="en_curso" {{ old('estatus', $servicioSocial->estatus) === 'en_curso' ? 'selected' : '' }}>En curso</option>
                <option value="completado" {{ old('estatus', $servicioSocial->estatus) === 'completado' ? 'selected' : '' }}>Completado</option>
            </select>
            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">Se ajustará automáticamente a "Completado" si las horas acumuladas alcanzan las requeridas.</p>
            @error('estatus') <p class="text-red-500 text-[11px] mt-1 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-between items-center pt-2">
            <a href="{{ route('gestor.servicio-social.index') }}" class="text-[12px] text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">&larr; Volver</a>
            <button type="submit" class="bg-[#0606F0] text-white px-6 py-2.5 rounded-xl text-[13px] font-medium hover:bg-[#04276B] transition-colors dark:bg-[#0606F0] dark:hover:bg-[#0606F0]">Actualizar</button>
        </div>
    </form>

</div>

</x-panel>
