<x-panel title="Detalle Horario" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    <div class="mb-5">
        <a href="{{ route('director.horarios.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver a Horarios
        </a>
    </div>

    <div class="max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-5">Horario #{{ $horario->id_horario }}</h3>
        <div class="grid grid-cols-2 gap-y-5 gap-x-8">
            <div>
                <p class="text-xs text-gray-400 mb-1">Grupo</p>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $horario->grupo?->clave_grupo ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Materia</p>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $horario->materia?->nombre_materia ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Docente</p>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $horario->docente?->nombre_completo ?? 'Sin docente' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Dia</p>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 capitalize">{{ $horario->dia_semana }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Hora Inicio</p>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Hora Fin</p>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}</p>
            </div>
        </div>
        <div class="flex gap-3 mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('director.horarios.edit', $horario->id_horario) }}" class="px-5 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-sm font-medium rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">Editar</a>
        </div>
    </div>
</x-panel>
