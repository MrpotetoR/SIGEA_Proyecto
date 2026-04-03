<x-panel title="Mi Perfil" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    @if(!$alumno)
        <div class="bg-yellow-50 dark:bg-amber-900/30 border border-yellow-300 dark:border-amber-700 text-yellow-800 dark:text-amber-300 rounded-lg p-6 text-center">
            No se encontró información de alumno vinculada a tu cuenta.
        </div>
    @else
        <div class="space-y-6">

            {{-- Encabezado --}}
            <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-6 flex items-center gap-6">
                <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-4xl flex-shrink-0">
                    👤
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $alumno->nombre_completo }}</h2>
                    <p class="text-[#0606F0] font-mono text-sm mt-1">Matrícula: {{ $alumno->matricula }}</p>
                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold
                        {{ $alumno->estatus === 'activo' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
                           ($alumno->estatus === 'baja_temporal' ? 'bg-yellow-100 text-yellow-800 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                        {{ ucwords(str_replace('_', ' ', $alumno->estatus)) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Datos académicos --}}
                <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-6">
                    <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 pb-3 mb-4">📚 Datos Académicos</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Carrera</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $alumno->carrera?->nombre_carrera ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Cuatrimestre Actual</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $alumno->cuatrimestre_actual ?? '—' }}°</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Promedio General</dt>
                            <dd class="text-sm font-bold text-blue-700">{{ $alumno->promedio_general }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Tutor asignado --}}
                <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-6">
                    <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 pb-3 mb-4">👨‍🏫 Tutor Asignado</h3>
                    @if($alumno->tutor)
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Nombre</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $alumno->tutor->nombre }} {{ $alumno->tutor->apellidos }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Especialidad</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $alumno->tutor->especialidad ?? '—' }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-sm text-gray-400 dark:text-gray-500">Sin tutor asignado.</p>
                    @endif
                </div>

                {{-- Cuenta del sistema --}}
                <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-6">
                    <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 pb-3 mb-4">🔐 Cuenta del Sistema</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Usuario</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $alumno->user?->name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Correo</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $alumno->user?->email ?? '—' }}</dd>
                        </div>
                    </dl>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-4">
                        * Para modificar tus datos personales, acude a Servicios Escolares.
                    </p>
                </div>

            </div>
        </div>
    @endif
</x-panel>
