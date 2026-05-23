<x-panel title="Mi Perfil" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">
        @include('partials.gestor-nav')
    </x-slot>

    <div class="max-w-3xl">
        {{-- Header perfil --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-8 mb-6">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-700 dark:text-blue-300 flex-shrink-0">
                    <x-icon name="user" class="w-10 h-10" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $docente->nombre_completo ?? 'Sin nombre' }}</h2>
                    <p class="text-sm text-[#0606F0] dark:text-blue-400 font-medium mt-1">Director de Carrera</p>
                    <p class="text-xs text-gray-400 mt-0.5">ID Docente: {{ $docente->id_docente ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Datos personales --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-[15px] font-semibold text-gray-800 dark:text-gray-200 mb-5">Informacion Personal</h3>
            <div class="grid grid-cols-2 gap-y-5 gap-x-8">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Nombre(s)</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $docente->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Apellidos</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $docente->apellidos ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Especialidad</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $docente->especialidad ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Tutor</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $docente ? ($docente->es_tutor ? 'Si' : 'No') : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Correo electronico</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $docente->user->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-panel>
