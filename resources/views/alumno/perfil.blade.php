<x-panel title="Mi Perfil" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    @if(!$alumno)
        <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg p-6 text-center">
            No se encontró información de alumno vinculada a tu cuenta.
        </div>
    @else
        <div class="space-y-6">

            {{-- Encabezado --}}
            <div class="bg-white rounded-xl shadow p-6 flex items-center gap-6">
                <div class="w-20 h-20 rounded-full bg-indigo-100 flex items-center justify-center text-4xl flex-shrink-0">
                    👤
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $alumno->nombre_completo }}</h2>
                    <p class="text-indigo-600 font-mono text-sm mt-1">Matrícula: {{ $alumno->matricula }}</p>
                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold
                        {{ $alumno->estatus === 'activo' ? 'bg-green-100 text-green-800' :
                           ($alumno->estatus === 'baja_temporal' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucwords(str_replace('_', ' ', $alumno->estatus)) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Datos académicos --}}
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-base font-semibold text-gray-700 border-b pb-3 mb-4">📚 Datos Académicos</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Carrera</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $alumno->carrera?->nombre_carrera ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Cuatrimestre Actual</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $alumno->cuatrimestre_actual ?? '—' }}°</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Promedio General</dt>
                            <dd class="text-sm font-bold text-indigo-700">{{ $alumno->promedio_general }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Tutor asignado --}}
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-base font-semibold text-gray-700 border-b pb-3 mb-4">👨‍🏫 Tutor Asignado</h3>
                    @if($alumno->tutor)
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Nombre</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $alumno->tutor->nombre }} {{ $alumno->tutor->apellidos }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Especialidad</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $alumno->tutor->especialidad ?? '—' }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-sm text-gray-400">Sin tutor asignado.</p>
                    @endif
                </div>

                {{-- Cuenta del sistema --}}
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-base font-semibold text-gray-700 border-b pb-3 mb-4">🔐 Cuenta del Sistema</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Usuario</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $alumno->user?->name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Correo</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $alumno->user?->email ?? '—' }}</dd>
                        </div>
                    </dl>
                    <p class="text-xs text-gray-400 mt-4">
                        * Para modificar tus datos personales, acude a Servicios Escolares.
                    </p>
                </div>

            </div>
        </div>
    @endif
</x-panel>
