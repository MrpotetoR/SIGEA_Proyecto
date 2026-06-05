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
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $gestor->nombre_completo ?? auth()->user()->name ?? 'Sin nombre' }}</h2>
                    <p class="text-sm text-[#0606F0] dark:text-blue-400 font-medium mt-1">Gestor Escolar</p>
                    <p class="text-xs text-gray-400 mt-0.5">ID Personal: {{ $gestor->id_personal ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Datos personales --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-[15px] font-semibold text-gray-800 dark:text-gray-200 mb-5">Informacion Personal</h3>
            <div class="grid grid-cols-2 gap-y-5 gap-x-8">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Nombre(s)</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $gestor->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Apellidos</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $gestor->apellidos ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Especialidad</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $gestor->especialidad ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">RFC</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $gestor->rfc ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Cedula profesional</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $gestor->num_cedula ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Correo electronico</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $gestor->user->email ?? auth()->user()->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Permisos y carreras asignadas --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6 mt-6">
            <h3 class="text-[15px] font-semibold text-gray-800 dark:text-gray-200 mb-5">Permisos y carreras</h3>
            <div class="grid grid-cols-2 gap-y-5 gap-x-8">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Puede asignar carreras</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $gestor && $gestor->puede_asignar_carreras ? 'Si' : 'No' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Puede gestionar Caja Chica</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $gestor && $gestor->puede_gestionar_caja_chica ? 'Si' : 'No' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-1">Carreras asignadas</p>
                    @php($carreras = $gestor ? $gestor->carreras : collect())
                    @if($carreras->count())
                        <div class="flex flex-wrap gap-2 mt-1">
                            @foreach($carreras as $carrera)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                                    {{ $carrera->nombre_carrera ?? $carrera->nombre ?? 'Carrera' }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Sin carreras asignadas</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-panel>
