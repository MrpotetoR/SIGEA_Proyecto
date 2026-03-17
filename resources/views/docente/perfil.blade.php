<x-panel title="Mi Perfil" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="max-w-3xl space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900">Mi Perfil</h1>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-5 mb-6">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-indigo-200">
                {{ strtoupper(substr($docente?->nombre ?? 'D', 0, 1)) }}{{ strtoupper(substr($docente?->apellidos ?? 'C', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-[18px] font-bold text-gray-900">{{ $docente?->nombre_completo ?? 'Sin datos' }}</h2>
                <p class="text-[13px] text-gray-400">Docente</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @php
            $campos = [
                ['label' => 'Nombre', 'value' => $docente?->nombre ?? '---'],
                ['label' => 'Apellidos', 'value' => $docente?->apellidos ?? '---'],
                ['label' => 'Especialidad', 'value' => $docente?->especialidad ?? '---'],
                ['label' => 'Horas Contrato', 'value' => ($docente?->horas_contrato ?? 0) . ' hrs'],
                ['label' => 'Tutor', 'value' => $docente?->es_tutor ? 'Si' : 'No'],
                ['label' => 'Correo', 'value' => auth()->user()->email],
            ];
            @endphp
            @foreach($campos as $campo)
                <div class="p-4 bg-gray-50/70 rounded-xl">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">{{ $campo['label'] }}</p>
                    <p class="text-[14px] font-medium text-gray-800">{{ $campo['value'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

</div>

</x-panel>
