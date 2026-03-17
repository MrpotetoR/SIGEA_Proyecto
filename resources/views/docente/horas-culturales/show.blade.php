<x-panel title="Detalle Horas ACUDE" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="max-w-2xl space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900">Detalle Horas ACUDE</h1>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-3">
        <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50/70 rounded-xl">
                <p class="text-[11px] font-semibold text-gray-400 uppercase mb-1">Alumno</p>
                <p class="text-[14px] font-medium text-gray-800">{{ $horasCultural->alumno?->nombre_completo ?? '---' }}</p>
            </div>
            <div class="p-4 bg-gray-50/70 rounded-xl">
                <p class="text-[11px] font-semibold text-gray-400 uppercase mb-1">Tipo</p>
                <p class="text-[14px] font-medium text-gray-800">{{ ucfirst($horasCultural->tipo) }}</p>
            </div>
            <div class="p-4 bg-gray-50/70 rounded-xl">
                <p class="text-[11px] font-semibold text-gray-400 uppercase mb-1">Horas</p>
                <p class="text-[14px] font-medium text-gray-800">{{ $horasCultural->horas_acumuladas }}</p>
            </div>
            <div class="p-4 bg-gray-50/70 rounded-xl">
                <p class="text-[11px] font-semibold text-gray-400 uppercase mb-1">Descripcion</p>
                <p class="text-[14px] font-medium text-gray-800">{{ $horasCultural->descripcion ?? '---' }}</p>
            </div>
        </div>
        <a href="{{ route('docente.horas-culturales.index') }}" class="inline-block text-[12px] text-gray-500 hover:text-gray-700 mt-3">&larr; Volver</a>
    </div>

</div>

</x-panel>
