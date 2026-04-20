<x-panel title="Mis Pagos" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    <div class="max-w-4xl space-y-6">

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-1">Bauchers de pago por cuatrimestre</h2>
            <p class="text-xs text-gray-400 mb-5">Sube tu baucher en PDF. Será revisado por Servicios Escolares antes de ser aprobado.</p>

            {{-- Leyenda de estados --}}
            <div class="flex flex-wrap gap-4 mb-5 text-xs text-gray-500 dark:text-gray-400">
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span> Aprobado
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span> Pendiente de revisión
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span> Rechazado
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <x-icon name="lock" class="w-3 h-3" /> Bloqueado
                </span>
            </div>

            @php $maxPeriodos = $alumno->carrera?->max_periodos ?? 10; @endphp
            <div class="space-y-3">
                @for($i = 1; $i <= $maxPeriodos; $i++)
                    @php
                        $pago = $pagos[$i] ?? null;
                        $esSiguiente = ($i === $siguiente);
                        $bloqueado = (!$pago && !$esSiguiente);
                    @endphp

                    <div class="flex items-center gap-4 p-4 rounded-xl border transition-colors
                        {{ $pago && $pago->estaAprobado() ? 'border-green-200 dark:border-green-800 bg-green-50/40 dark:bg-green-900/10'
                          : ($pago && $pago->estaPendiente() ? 'border-amber-200 dark:border-amber-800 bg-amber-50/40 dark:bg-amber-900/10'
                          : ($pago && $pago->estaRechazado() ? 'border-red-200 dark:border-red-800 bg-red-50/40 dark:bg-red-900/10'
                          : ($esSiguiente ? 'border-blue-200 dark:border-blue-700 bg-blue-50/30 dark:bg-blue-900/10'
                          : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/20 opacity-50'))) }}">

                        {{-- Número de cuatrimestre --}}
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0
                            {{ $pago && $pago->estaAprobado() ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300'
                              : ($pago && $pago->estaPendiente() ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300'
                              : ($pago && $pago->estaRechazado() ? 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300'
                              : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400')) }}">
                            {{ $i }}°
                        </div>

                        {{-- Contenido --}}
                        <div class="flex-1 min-w-0">
                            @if($pago && $pago->estaAprobado())
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1 text-sm font-medium text-green-700 dark:text-green-300">
                                        <x-icon name="check-circle" class="w-4 h-4" /> Aprobado
                                    </span>
                                    @if($pago->revisado_en)
                                        <span class="text-[10px] text-gray-400">{{ $pago->revisado_en->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            @elseif($pago && $pago->estaPendiente())
                                <span class="inline-flex items-center gap-1 text-sm font-medium text-amber-700 dark:text-amber-300">
                                    <x-icon name="clock" class="w-4 h-4" /> Pendiente de revisión
                                </span>
                                <p class="text-[10px] text-gray-400 mt-0.5">Subido el {{ \Carbon\Carbon::parse($pago->subido_en)->format('d/m/Y H:i') }}</p>
                            @elseif($pago && $pago->estaRechazado())
                                <span class="inline-flex items-center gap-1 text-sm font-medium text-red-700 dark:text-red-300">
                                    <x-icon name="warning" class="w-4 h-4" /> Rechazado
                                </span>
                                @if($pago->comentario_rechazo)
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-1 bg-red-50 dark:bg-red-900/20 px-3 py-2 rounded-lg">
                                        <strong>Observaciones:</strong> {{ $pago->comentario_rechazo }}
                                    </p>
                                @endif
                            @elseif($esSiguiente)
                                <span class="text-sm text-gray-500 dark:text-gray-400">Disponible para carga</span>
                            @else
                                <span class="inline-flex items-center gap-1 text-sm text-gray-400">
                                    <x-icon name="lock" class="w-3.5 h-3.5" /> Debes completar el cuatrimestre anterior
                                </span>
                            @endif
                        </div>

                        {{-- Acciones --}}
                        <div class="flex-shrink-0 flex items-center gap-2">
                            @if($pago && ($pago->estaAprobado() || $pago->estaPendiente()))
                                <a href="{{ asset('storage/'.$pago->baucher_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-[#0606F0] dark:text-blue-400 hover:underline text-xs">
                                    <x-icon name="document" class="w-4 h-4" /> Ver PDF
                                </a>
                            @elseif($pago && $pago->estaRechazado())
                                {{-- Re-subir --}}
                                <form method="POST" action="{{ route('alumno.pagos.store') }}" enctype="multipart/form-data"
                                      class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="cuatrimestre" value="{{ $i }}">
                                    <input type="file" name="baucher" accept="application/pdf" required
                                           class="w-36 text-[10px] text-gray-500 file:mr-1 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-medium file:bg-red-50 file:text-red-700 dark:file:bg-gray-700 dark:file:text-red-300">
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-[10px] font-semibold px-3 py-1.5 rounded inline-flex items-center gap-1">
                                        <x-icon name="plus" class="w-3 h-3" /> Resubir
                                    </button>
                                </form>
                            @elseif($esSiguiente)
                                <form method="POST" action="{{ route('alumno.pagos.store') }}" enctype="multipart/form-data"
                                      class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="cuatrimestre" value="{{ $i }}">
                                    <input type="file" name="baucher" accept="application/pdf" required
                                           class="w-36 text-[10px] text-gray-500 file:mr-1 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-medium file:bg-blue-50 file:text-blue-700 dark:file:bg-gray-700 dark:file:text-blue-300">
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

    </div>
</x-panel>
