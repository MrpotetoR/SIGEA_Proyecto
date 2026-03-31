<x-panel title="Detalle Servicio Social" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="max-w-2xl space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Detalle Servicio Social</h1>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-3 dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20">
        <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50/70 rounded-xl dark:bg-gray-700/50">
                <p class="text-[11px] font-semibold text-gray-400 uppercase mb-1 dark:text-gray-500">Alumno</p>
                <p class="text-[14px] font-medium text-gray-800 dark:text-gray-200">{{ $servicioSocial->alumno?->nombre_completo ?? '---' }}</p>
            </div>
            <div class="p-4 bg-gray-50/70 rounded-xl dark:bg-gray-700/50">
                <p class="text-[11px] font-semibold text-gray-400 uppercase mb-1 dark:text-gray-500">Estatus</p>
                <p class="text-[14px] font-medium text-gray-800 dark:text-gray-200">{{ ucfirst($servicioSocial->estatus ?? '---') }}</p>
            </div>
            <div class="p-4 bg-gray-50/70 rounded-xl dark:bg-gray-700/50">
                <p class="text-[11px] font-semibold text-gray-400 uppercase mb-1 dark:text-gray-500">Horas</p>
                <p class="text-[14px] font-medium text-gray-800 dark:text-gray-200">{{ $servicioSocial->horas_acumuladas ?? 0 }}</p>
            </div>
            <div class="p-4 bg-gray-50/70 rounded-xl dark:bg-gray-700/50">
                <p class="text-[11px] font-semibold text-gray-400 uppercase mb-1 dark:text-gray-500">Institucion</p>
                <p class="text-[14px] font-medium text-gray-800 dark:text-gray-200">{{ $servicioSocial->institucion ?? '---' }}</p>
            </div>
        </div>
        <a href="{{ route('docente.servicio-social.index') }}" class="inline-block text-[12px] text-gray-500 hover:text-gray-700 mt-3 dark:text-gray-400 dark:hover:text-gray-300">&larr; Volver</a>
    </div>

</div>

</x-panel>
