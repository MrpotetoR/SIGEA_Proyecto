<x-panel title="Detalle Servicio Social" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="max-w-2xl space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900">Detalle Servicio Social</h1>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-3">
        <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50/70 rounded-xl">
                <p class="text-[11px] font-semibold text-gray-400 uppercase mb-1">Alumno</p>
                <p class="text-[14px] font-medium text-gray-800">{{ $servicioSocial->alumno?->nombre_completo ?? '---' }}</p>
            </div>
            <div class="p-4 bg-gray-50/70 rounded-xl">
                <p class="text-[11px] font-semibold text-gray-400 uppercase mb-1">Estatus</p>
                <p class="text-[14px] font-medium text-gray-800">{{ ucfirst($servicioSocial->estatus ?? '---') }}</p>
            </div>
            <div class="p-4 bg-gray-50/70 rounded-xl">
                <p class="text-[11px] font-semibold text-gray-400 uppercase mb-1">Horas</p>
                <p class="text-[14px] font-medium text-gray-800">{{ $servicioSocial->horas_acumuladas ?? 0 }}</p>
            </div>
            <div class="p-4 bg-gray-50/70 rounded-xl">
                <p class="text-[11px] font-semibold text-gray-400 uppercase mb-1">Institucion</p>
                <p class="text-[14px] font-medium text-gray-800">{{ $servicioSocial->institucion ?? '---' }}</p>
            </div>
        </div>
        <a href="{{ route('docente.servicio-social.index') }}" class="inline-block text-[12px] text-gray-500 hover:text-gray-700 mt-3">&larr; Volver</a>
    </div>

</div>

</x-panel>
