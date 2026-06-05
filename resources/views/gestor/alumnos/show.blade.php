<x-panel title="Detalle Alumno" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="max-w-5xl space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('gestor.alumnos.index') }}"
               class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline">← Volver a la lista</a>
            <a href="{{ route('gestor.alumnos.edit', $alumno) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Editar
            </a>
        </div>

        {{-- Datos principales --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $alumno->nombre_completo }}</h2>
                    <p class="text-[#0606F0] dark:text-blue-400 font-mono text-sm mt-1">{{ $alumno->id_alumno_publico }}</p>
                </div>
                @php
                    $badge = match($alumno->estatus) {
                        'activo' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                        'baja_temporal' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                        default => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $badge }}">
                    {{ ucfirst(str_replace('_', ' ', $alumno->estatus)) }}
                </span>
            </div>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Correo</dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $alumno->user?->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Carrera</dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $alumno->carrera?->nombre_carrera ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Cuatrimestre</dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $alumno->cuatrimestre_actual }}°</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Tutor</dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $alumno->tutor?->nombre_completo ?? 'Sin asignar' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Padre / Tutor --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">Datos del padre o tutor</h3>
            @if($alumno->padreTutor)
                @php $p = $alumno->padreTutor; @endphp
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500 dark:text-gray-400">Nombre completo</dt>
                         <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $p->nombre }} {{ $p->apellidos }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Correo</dt>
                         <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $p->email ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Teléfono</dt>
                         <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $p->telefono ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">N° emergencia</dt>
                         <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $p->telefono_emergencia ?? '—' }}</dd></div>
                    <div class="col-span-2"><dt class="text-gray-500 dark:text-gray-400">INE</dt>
                        <dd>
                            @if($p->ine_path)
                                <a href="{{ asset('storage/'.$p->ine_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-[#0606F0] dark:text-blue-400 hover:underline text-sm">
                                    <x-icon name="document" class="w-4 h-4" /> Ver / descargar INE
                                </a>
                            @else
                                <span class="text-gray-400">Sin archivo</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            @else
                <p class="text-sm text-gray-400">No se ha registrado información del padre o tutor.</p>
            @endif
        </div>

        {{-- Pagos por cuatrimestre --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-1">Váuchers de pago por cuatrimestre</h3>
            <p class="text-xs text-gray-400 mb-4">Revisa, aprueba o rechaza los váuchers cargados por el alumno.</p>

            @if(session('error'))
                <div class="mb-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-2 rounded-lg text-xs">{{ session('error') }}</div>
            @endif

            @php
                $pagosByCuatri = $alumno->pagosCuatrimestre->keyBy('cuatrimestre');
                $maxAprobado = $alumno->pagosCuatrimestre->where('estatus', 'aprobado')->max('cuatrimestre') ?? 0;
                $hayPendiente = $alumno->pagosCuatrimestre->where('estatus', 'pendiente')->isNotEmpty();
                $siguiente = $hayPendiente ? null : ($maxAprobado + 1);
                $maxPeriodos = $alumno->carrera?->max_periodos ?? 10;
            @endphp

            <div class="space-y-3">
                @for($i = 1; $i <= $maxPeriodos; $i++)
                    @php
                        $pago = $pagosByCuatri[$i] ?? null;
                        $esSiguiente = ($i === $siguiente);
                        $bloqueado = (!$pago && !$esSiguiente);
                    @endphp
                    <div class="flex items-start gap-4 p-4 rounded-xl border transition-colors
                        {{ $pago && $pago->estaAprobado() ? 'border-green-200 dark:border-green-800 bg-green-50/40 dark:bg-green-900/10'
                          : ($pago && $pago->estaPendiente() ? 'border-amber-200 dark:border-amber-800 bg-amber-50/40 dark:bg-amber-900/10'
                          : ($pago && $pago->estaRechazado() ? 'border-red-200 dark:border-red-800 bg-red-50/40 dark:bg-red-900/10'
                          : ($esSiguiente ? 'border-blue-200 dark:border-blue-700 bg-blue-50/30 dark:bg-blue-900/10'
                          : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/20 opacity-50'))) }}">

                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0
                            {{ $pago && $pago->estaAprobado() ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300'
                              : ($pago && $pago->estaPendiente() ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300'
                              : ($pago && $pago->estaRechazado() ? 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300'
                              : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400')) }}">
                            {{ $i }}°
                        </div>

                        <div class="flex-1 min-w-0">
                            @if($pago && $pago->estaAprobado())
                                <span class="inline-flex items-center gap-1 text-sm font-medium text-green-700 dark:text-green-300">
                                    <x-icon name="check-circle" class="w-4 h-4" /> Aprobado
                                </span>
                                @if($pago->revisado_en)
                                    <span class="text-[10px] text-gray-400 ml-2">{{ $pago->revisado_en->format('d/m/Y') }}</span>
                                @endif
                            @elseif($pago && $pago->estaPendiente())
                                <span class="inline-flex items-center gap-1 text-sm font-medium text-amber-700 dark:text-amber-300">
                                    <x-icon name="clock" class="w-4 h-4" /> Pendiente de revisión
                                </span>
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    Subido por el alumno el {{ \Carbon\Carbon::parse($pago->subido_en)->format('d/m/Y H:i') }}
                                </p>
                            @elseif($pago && $pago->estaRechazado())
                                <span class="inline-flex items-center gap-1 text-sm font-medium text-red-700 dark:text-red-300">
                                    <x-icon name="warning" class="w-4 h-4" /> Rechazado
                                </span>
                                @if($pago->comentario_rechazo)
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $pago->comentario_rechazo }}</p>
                                @endif
                            @elseif($esSiguiente)
                                <span class="text-sm text-gray-500">Sin váucher cargado</span>
                            @else
                                <span class="inline-flex items-center gap-1 text-sm text-gray-400">
                                    <x-icon name="lock" class="w-3.5 h-3.5" /> Bloqueado
                                </span>
                            @endif
                        </div>

                        {{-- Acciones --}}
                        <div class="flex-shrink-0 flex flex-col items-end gap-2">
                            @if($pago)
                                <a href="{{ asset('storage/'.$pago->baucher_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-[#0606F0] dark:text-blue-400 hover:underline text-xs">
                                    <x-icon name="document" class="w-3.5 h-3.5" /> Ver PDF
                                </a>
                            @endif

                            @if($pago && $pago->estaPendiente())
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('gestor.pagos.aprobar', $pago) }}"
                                          data-udea-confirm
                                          data-confirm-title="Aprobar váucher"
                                          data-confirm-message="¿Aprobar este váucher?"
                                          data-confirm-detail="Se registrará el pago como aprobado y se generará el ingreso correspondiente."
                                          data-confirm-variant="success"
                                          data-confirm-icon="check"
                                          data-confirm-button="Aprobar"
                                          data-confirm-cancel="Cancelar">
                                        @csrf
                                        <button type="submit"
                                                class="bg-green-600 hover:bg-green-700 text-white text-[10px] font-semibold px-3 py-1.5 rounded inline-flex items-center gap-1">
                                            <x-icon name="check" class="w-3 h-3" /> Aprobar
                                        </button>
                                    </form>
                                    <button type="button" onclick="this.closest('.flex-col').querySelector('.rechazo-form').classList.toggle('hidden')"
                                            class="bg-red-600 hover:bg-red-700 text-white text-[10px] font-semibold px-3 py-1.5 rounded inline-flex items-center gap-1">
                                        <x-icon name="warning" class="w-3 h-3" /> Rechazar
                                    </button>
                                </div>
                                <form method="POST" action="{{ route('gestor.pagos.rechazar', $pago) }}"
                                      class="rechazo-form hidden w-full mt-1"
                                      data-udea-confirm
                                      data-confirm-title="Rechazar váucher"
                                      data-confirm-message="¿Rechazar este váucher?"
                                      data-confirm-detail="El alumno deberá enviar un nuevo comprobante de pago."
                                      data-confirm-variant="danger"
                                      data-confirm-icon="x-circle"
                                      data-confirm-button="Rechazar"
                                      data-confirm-cancel="Cancelar">
                                    @csrf
                                    <textarea name="comentario_rechazo" required maxlength="500" rows="2"
                                              placeholder="Escribe las observaciones del rechazo..."
                                              class="w-full text-xs border border-red-200 dark:border-red-700 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-400 focus:outline-none mb-1"></textarea>
                                    <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white text-[10px] font-semibold px-4 py-1.5 rounded">
                                        Confirmar rechazo
                                    </button>
                                </form>
                            @endif

                            @if(!$pago && $esSiguiente)
                                <form method="POST" action="{{ route('gestor.alumnos.baucher', $alumno) }}"
                                      enctype="multipart/form-data" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="cuatrimestre" value="{{ $i }}">
                                    <input type="file" name="baucher" accept="application/pdf" required
                                           class="w-36 text-[10px] text-gray-500 file:mr-1 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-medium file:bg-blue-100 file:text-blue-700 dark:file:bg-gray-700 dark:file:text-blue-300">
                                    <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white text-[10px] font-semibold px-3 py-1.5 rounded inline-flex items-center gap-1">
                                        <x-icon name="plus" class="w-3 h-3" /> Subir
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Documentación --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">Documentación del alumno</h3>
            @php $docsByTipo = $alumno->documentos->keyBy('tipo'); @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach(\App\Models\DocumentoAlumno::TIPOS as $tipo => $label)
                    @php $doc = $docsByTipo[$tipo] ?? null; @endphp
                    <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                        @if($doc)
                            <a href="{{ asset('storage/'.$doc->archivo_path) }}" target="_blank"
                               class="inline-flex items-center gap-1 text-[#0606F0] dark:text-blue-400 hover:underline text-xs">
                                <x-icon name="document" class="w-3.5 h-3.5" /> Ver / descargar
                            </a>
                        @else
                            <span class="text-xs text-gray-400">Sin archivo</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Acciones de baja / reingreso --}}
        @if($alumno->estatus === 'activo')
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">Registrar baja</h3>
                <form method="POST" action="{{ route('gestor.alumnos.baja', $alumno) }}" class="space-y-3"
                      data-udea-confirm
                      data-confirm-title="Registrar baja"
                      data-confirm-message="¿Confirmar la baja de <strong>{{ $alumno->nombre_completo }}</strong>?"
                      data-confirm-detail="Esta acción afectará el estatus académico del alumno."
                      data-confirm-variant="danger"
                      data-confirm-icon="warning"
                      data-confirm-button="Registrar baja"
                      data-confirm-cancel="Cancelar">
                    @csrf
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Tipo de baja</label>
                            <select name="tipo_baja" required
                                    class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                                <option value="temporal">Temporal</option>
                                <option value="definitiva">Definitiva</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Fecha de baja</label>
                            <input type="date" name="fecha_baja" value="{{ today()->toDateString() }}" required
                                   class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Motivo</label>
                            <input type="text" name="motivo" required placeholder="Motivo de la baja"
                                   class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:placeholder-gray-400">
                        </div>
                    </div>
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Registrar baja
                    </button>
                </form>
            </div>
        @elseif($alumno->estatus === 'baja_temporal')
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">Registrar reingreso</h3>
                <form method="POST" action="{{ route('gestor.alumnos.reingreso', $alumno) }}" class="flex gap-3 items-end">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Fecha de reingreso</label>
                        <input type="date" name="fecha_reingreso" value="{{ today()->toDateString() }}" required
                               class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-3">Inscripciones</h3>
                <div class="space-y-2">
                    @foreach($alumno->inscripciones as $insc)
                        <div class="flex justify-between items-center text-sm border-b dark:border-gray-700 pb-2">
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $insc->grupo?->clave_grupo }}</span>
                            <span class="text-gray-500 dark:text-gray-400">{{ $insc->fecha_inscripcion?->format('d/m/Y') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Constancias --}}
        @if($alumno->constancias->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-3">Constancias emitidas</h3>
                <div class="space-y-2">
                    @foreach($alumno->constancias as $c)
                        <div class="flex justify-between items-center text-sm border-b dark:border-gray-700 pb-2">
                            <span class="capitalize text-gray-800 dark:text-gray-200">{{ str_replace('_', ' ', $c->tipo) }}</span>
                            <div class="flex gap-3 items-center">
                                <span class="text-gray-400">{{ $c->fecha_emision?->format('d/m/Y') }}</span>
                                <a href="{{ route('gestor.constancias.pdf', $c) }}"
                                   class="text-[#0606F0] dark:text-blue-400 hover:underline text-xs">Descargar PDF</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-panel>
