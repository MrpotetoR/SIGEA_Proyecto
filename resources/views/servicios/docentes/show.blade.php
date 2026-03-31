<x-panel title="Detalle Docente" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-3xl space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('servicios.docentes.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← Volver</a>
            <a href="{{ route('servicios.docentes.edit', $docente) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Editar
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $docente->nombre_completo }}</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-gray-500 dark:text-gray-400">Correo</dt><dd class="font-medium dark:text-gray-200">{{ $docente->user?->email }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Especialidad</dt><dd class="font-medium dark:text-gray-200">{{ $docente->especialidad ?? '—' }}</dd></div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Contrato</dt>
                    <dd class="font-medium dark:text-gray-200">
                        @if(is_null($docente->horas_contrato))
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Docente de Planta</span>
                        @else
                            {{ $docente->horas_contrato }} horas semanales
                        @endif
                    </dd>
                </div>
                <div><dt class="text-gray-500 dark:text-gray-400">Es tutor</dt><dd class="font-medium dark:text-gray-200">{{ $docente->es_tutor ? 'Sí' : 'No' }}</dd></div>
            </dl>
        </div>

        @if($docente->horarios->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-3">Horarios asignados</h3>
                <div class="space-y-2 text-sm">
                    @foreach($docente->horarios as $h)
                        <div class="flex justify-between border-b dark:border-gray-700 pb-2">
                            <span class="font-medium dark:text-gray-200">{{ $h->materia?->nombre_materia }}</span>
                            <span class="text-gray-500 dark:text-gray-400">{{ $h->grupo?->clave_grupo }} — {{ ucfirst($h->dia_semana) }} {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }}-{{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-panel>
