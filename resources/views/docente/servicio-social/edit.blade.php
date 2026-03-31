<x-panel title="Editar Servicio Social" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="max-w-2xl space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Editar Servicio Social</h1>

    <form method="POST" action="{{ route('docente.servicio-social.update', $servicioSocial->id_servicio) }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4 dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20">
        @csrf @method('PUT')

        <div>
            <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block dark:text-gray-400">Alumno</label>
            <p class="text-[14px] font-medium text-gray-800 bg-gray-50 rounded-xl px-3 py-2.5 dark:text-gray-200 dark:bg-gray-700/50">{{ $servicioSocial->alumno?->nombre_completo }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block dark:text-gray-400">Horas Acumuladas</label>
                <input type="number" name="horas_acumuladas" value="{{ $servicioSocial->horas_acumuladas }}" min="0" step="1" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-violet-300 outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
            </div>
            <div>
                <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block dark:text-gray-400">Estatus</label>
                <select name="estatus" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-violet-300 outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    <option value="en_curso" {{ $servicioSocial->estatus === 'en_curso' ? 'selected' : '' }}>En curso</option>
                    <option value="completado" {{ $servicioSocial->estatus === 'completado' ? 'selected' : '' }}>Completado</option>
                </select>
            </div>
        </div>

        <div>
            <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block dark:text-gray-400">Institucion</label>
            <input type="text" name="institucion" value="{{ $servicioSocial->institucion }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-violet-300 outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
        </div>

        <div class="flex justify-between items-center pt-2">
            <a href="{{ route('docente.servicio-social.index') }}" class="text-[12px] text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">&larr; Volver</a>
            <button type="submit" class="bg-gray-900 text-white px-6 py-2.5 rounded-xl text-[13px] font-medium hover:bg-gray-700 transition-colors dark:bg-indigo-600 dark:hover:bg-indigo-500">Actualizar</button>
        </div>
    </form>

</div>

</x-panel>
