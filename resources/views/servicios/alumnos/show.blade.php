<x-panel title="Detalle Alumno" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-3xl space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('servicios.alumnos.index') }}"
               class="text-sm text-indigo-600 hover:underline">← Volver a la lista</a>
            <a href="{{ route('servicios.alumnos.edit', $alumno) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Editar
            </a>
        </div>

        {{-- Datos principales --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $alumno->nombre_completo }}</h2>
                    <p class="text-indigo-600 font-mono text-sm mt-1">{{ $alumno->matricula }}</p>
                </div>
                @php
                    $badge = match($alumno->estatus) {
                        'activo' => 'bg-green-100 text-green-800',
                        'baja_temporal' => 'bg-yellow-100 text-yellow-800',
                        default => 'bg-red-100 text-red-800',
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $badge }}">
                    {{ ucfirst(str_replace('_', ' ', $alumno->estatus)) }}
                </span>
            </div>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">Correo</dt>
                    <dd class="font-medium text-gray-800">{{ $alumno->user?->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Carrera</dt>
                    <dd class="font-medium text-gray-800">{{ $alumno->carrera?->nombre_carrera ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Cuatrimestre</dt>
                    <dd class="font-medium text-gray-800">{{ $alumno->cuatrimestre_actual }}°</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Tutor</dt>
                    <dd class="font-medium text-gray-800">{{ $alumno->tutor?->nombre_completo ?? 'Sin asignar' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Acciones de baja / reingreso --}}
        @if($alumno->estatus === 'activo')
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-4">Registrar baja</h3>
                <form method="POST" action="{{ route('servicios.alumnos.baja', $alumno) }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Tipo de baja</label>
                            <select name="tipo_baja" required
                                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                                <option value="temporal">Temporal</option>
                                <option value="definitiva">Definitiva</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Fecha de baja</label>
                            <input type="date" name="fecha_baja" value="{{ today()->toDateString() }}" required
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Motivo</label>
                            <input type="text" name="motivo" required placeholder="Motivo de la baja"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        </div>
                    </div>
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            onclick="return confirm('¿Confirmar baja del alumno?')">
                        Registrar baja
                    </button>
                </form>
            </div>
        @elseif($alumno->estatus === 'baja_temporal')
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-4">Registrar reingreso</h3>
                <form method="POST" action="{{ route('servicios.alumnos.reingreso', $alumno) }}" class="flex gap-3 items-end">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Fecha de reingreso</label>
                        <input type="date" name="fecha_reingreso" value="{{ today()->toDateString() }}" required
                               class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                    </div>
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Registrar reingreso
                    </button>
                </form>
            </div>
        @endif

        {{-- Grupos inscritos --}}
        @if($alumno->inscripciones->isNotEmpty())
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-3">Inscripciones</h3>
                <div class="space-y-2">
                    @foreach($alumno->inscripciones as $insc)
                        <div class="flex justify-between items-center text-sm border-b pb-2">
                            <span class="font-medium">{{ $insc->grupo?->clave_grupo }}</span>
                            <span class="text-gray-500">{{ $insc->fecha_inscripcion?->format('d/m/Y') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Constancias --}}
        @if($alumno->constancias->isNotEmpty())
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-3">Constancias emitidas</h3>
                <div class="space-y-2">
                    @foreach($alumno->constancias as $c)
                        <div class="flex justify-between items-center text-sm border-b pb-2">
                            <span class="capitalize">{{ str_replace('_', ' ', $c->tipo) }}</span>
                            <div class="flex gap-3 items-center">
                                <span class="text-gray-400">{{ $c->fecha_emision?->format('d/m/Y') }}</span>
                                <a href="{{ route('servicios.constancias.pdf', $c) }}"
                                   class="text-indigo-600 hover:underline text-xs">Descargar PDF</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-panel>
