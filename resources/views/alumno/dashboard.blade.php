<x-panel title="Overview" panelNombre="Panel Alumno">
<x-slot name="nav">@include('partials.alumno-nav')</x-slot>

<div class="flex gap-6">

    {{-- ============== COLUMNA PRINCIPAL ============== --}}
    <div class="flex-1 min-w-0 space-y-5">

        {{-- Saludo --}}
        <div>
            <h1 class="text-[26px] font-bold text-gray-900 leading-tight">
                @php
                    $hora = now()->hour;
                    $saludo = $hora < 12 ? 'Buenos días' : ($hora < 18 ? 'Buenas tardes' : 'Buenas noches');
                @endphp
                {{ $saludo }}, {{ $alumno?->nombre ?? auth()->user()->name }}
            </h1>
            <p class="text-[13px] text-gray-400 mt-1">Aquí puedes ver tu estado académico y actividad reciente.</p>
        </div>

        {{-- Semáforo académico --}}
        @if($alumno && $semaforo)
            @php
                $nivel = $semaforo->nivel;
                $sStyle = match($nivel) {
                    'verde'    => ['bg' => 'bg-emerald-50/70', 'border' => 'border-emerald-200/60', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
                    'amarillo' => ['bg' => 'bg-amber-50/70',   'border' => 'border-amber-200/60',   'text' => 'text-amber-700',   'dot' => 'bg-amber-500'],
                    'rojo'     => ['bg' => 'bg-red-50/70',     'border' => 'border-red-200/60',     'text' => 'text-red-700',     'dot' => 'bg-red-500'],
                    default    => ['bg' => 'bg-gray-50/70',    'border' => 'border-gray-200/60',    'text' => 'text-gray-700',    'dot' => 'bg-gray-500'],
                };
            @endphp
            <div class="rounded-2xl border {{ $sStyle['bg'] }} {{ $sStyle['border'] }} p-4 flex items-center gap-4 card-hover">
                <div class="w-10 h-10 rounded-xl {{ $sStyle['dot'] }} flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-[14px] {{ $sStyle['text'] }}">Semáforo Académico: {{ ucfirst($nivel) }}</p>
                    <p class="text-[12px] text-gray-500 mt-0.5">
                        Promedio: <strong class="text-gray-700">{{ $semaforo->promedio_calificaciones }}</strong>
                        <span class="mx-1.5 text-gray-300">|</span>
                        Asistencia: <strong class="text-gray-700">{{ $semaforo->porcentaje_asistencia }}%</strong>
                    </p>
                </div>
            </div>
        @endif

        {{-- Cards de resumen (estilo mockup: borde sutil, 3 columnas con datos e ícono) --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                {{-- Progress / Matrícula --}}
                <div class="p-5 card-hover">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                                </svg>
                            </span>
                            <span class="text-[13px] font-semibold text-gray-700">Matrícula</span>
                        </div>
                    </div>
                    <p class="text-[22px] font-bold text-gray-900 leading-none">{{ $alumno?->matricula ?? '—' }}</p>
                    <p class="text-[11px] text-gray-400 mt-1.5">{{ $alumno?->carrera?->nombre_carrera ?? 'Sin carrera asignada' }}</p>
                </div>

                {{-- Cuatrimestre --}}
                <div class="p-5 card-hover">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-violet-100 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </span>
                            <span class="text-[13px] font-semibold text-gray-700">Cuatrimestre</span>
                        </div>
                        <span class="text-[11px] text-gray-400">de 9</span>
                    </div>
                    <p class="text-[22px] font-bold text-gray-900 leading-none">{{ $alumno?->cuatrimestre_actual ?? '—' }}°</p>
                    <div class="mt-2.5 w-full bg-gray-100 rounded-full h-1.5">
                        @php $pct = min(100, (($alumno?->cuatrimestre_actual ?? 0) / 9) * 100); @endphp
                        <div class="progress-bar bg-violet-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>

                {{-- Clases hoy --}}
                <div class="p-5 card-hover">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-sky-100 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <span class="text-[13px] font-semibold text-gray-700">Clases Hoy</span>
                        </div>
                    </div>
                    <p class="text-[22px] font-bold text-gray-900 leading-none">{{ $proximasClases->count() }}</p>
                    <p class="text-[11px] text-gray-400 mt-1.5 capitalize">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM') }}</p>
                </div>
            </div>
        </div>

        {{-- Grid: Clases + Noticias --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Clases de hoy --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm card-hover">
                <div class="flex items-center justify-between px-5 pt-5 pb-3">
                    <h2 class="text-[15px] font-bold text-gray-800">Clases de Hoy</h2>
                    <a href="{{ route('alumno.horario') }}"
                       class="text-[11px] font-medium text-gray-400 hover:text-gray-700 px-2.5 py-1 rounded-lg hover:bg-gray-50">
                        Ver horario →
                    </a>
                </div>

                <div class="px-5 pb-5">
                    @if($proximasClases->isNotEmpty())
                        <div class="space-y-1">
                            @php
                                $colors = ['bg-violet-100 border-violet-300 text-violet-700', 'bg-emerald-100 border-emerald-300 text-emerald-700', 'bg-amber-100 border-amber-300 text-amber-700', 'bg-sky-100 border-sky-300 text-sky-700', 'bg-rose-100 border-rose-300 text-rose-700'];
                            @endphp
                            @foreach($proximasClases as $i => $clase)
                                @php $colorClass = $colors[$i % count($colors)]; @endphp
                                <div class="flex items-center gap-3 p-3 rounded-xl {{ explode(' ', $colorClass)[0] }}/40 border border-transparent hover:border-gray-200">
                                    <div class="w-8 h-8 rounded-lg {{ explode(' ', $colorClass)[0] }} flex items-center justify-center text-[11px] font-bold {{ explode(' ', $colorClass)[2] }}">
                                        {{ \Carbon\Carbon::parse($clase->hora_inicio)->format('H:i') }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[13px] font-semibold text-gray-800 truncate">{{ $clase->materia->nombre_materia }}</p>
                                        <p class="text-[11px] text-gray-400 truncate">{{ $clase->docente->nombre_completo }}</p>
                                    </div>
                                    <span class="text-[10px] font-mono text-gray-400">
                                        {{ \Carbon\Carbon::parse($clase->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($clase->hora_fin)->format('H:i') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-[13px] text-gray-400">Sin clases programadas para hoy</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Noticias recientes --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm card-hover">
                <div class="flex items-center justify-between px-5 pt-5 pb-3">
                    <h2 class="text-[15px] font-bold text-gray-800">Noticias Recientes</h2>
                    <a href="{{ route('alumno.noticias') }}"
                       class="text-[11px] font-medium text-gray-400 hover:text-gray-700 px-2.5 py-1 rounded-lg hover:bg-gray-50">
                        Ver todas →
                    </a>
                </div>

                <div class="px-5 pb-5">
                    @if($noticias->isNotEmpty())
                        <div class="space-y-1">
                            @foreach($noticias as $noticia)
                                <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50/70">
                                    <div class="w-2 h-2 rounded-full bg-indigo-400 mt-1.5 flex-shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[13px] font-semibold text-gray-800 truncate">{{ $noticia->titulo }}</p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $noticia->fecha_publicacion->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2"/>
                                </svg>
                            </div>
                            <p class="text-[13px] text-gray-400">Sin noticias recientes</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ============== PANEL IA (estilo mockup) ============== --}}
    <div class="w-[300px] flex-shrink-0 hidden xl:flex flex-col bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden"
         style="height: fit-content; max-height: calc(100vh - 120px);">

        {{-- Header --}}
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <span class="text-[13px] font-bold text-gray-800">Asistente SIGEA</span>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-semibold bg-gray-900 text-white px-2.5 py-0.5 rounded-full">IA</span>
            </div>
        </div>

        {{-- Mensajes --}}
        <div id="chat-messages" class="flex-1 overflow-y-auto custom-scrollbar p-5 space-y-3" style="min-height: 300px; max-height: 420px;">
            {{-- Bienvenida --}}
            <div class="text-center py-4" id="chat-welcome">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center mb-4 shadow-lg shadow-indigo-200">
                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <h3 class="text-[16px] font-bold text-gray-800 mb-1">¿En qué te ayudo?</h3>
                <p class="text-[11px] text-gray-400 leading-relaxed px-2">Pregunta sobre calificaciones, horario, horas ACUDE y más.</p>
            </div>

            {{-- Sugerencias rápidas --}}
            <div class="flex flex-wrap gap-1.5 justify-center" id="sugerencias">
                @foreach(['Calificaciones', 'Horas ACUDE', 'Horario', 'Servicio Social', 'Kardex', 'Docentes'] as $sug)
                    <button onclick="enviarSugerencia(this)"
                        class="chip text-[11px] font-medium bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-1.5 rounded-full">
                        {{ $sug }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Input --}}
        <div class="p-4 border-t border-gray-100">
            <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3.5 py-2.5">
                <input
                    type="text"
                    id="chat-input"
                    placeholder="Escribe tu pregunta..."
                    class="flex-1 bg-transparent text-[13px] text-gray-700 outline-none placeholder-gray-400"
                    onkeydown="if(event.key==='Enter') enviarMensaje()"
                />
                <button onclick="enviarMensaje()"
                    class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center hover:bg-gray-700 flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function agregarMensaje(texto, esUsuario) {
    const c = document.getElementById('chat-messages');
    const d = document.createElement('div');
    d.className = 'chat-msg flex ' + (esUsuario ? 'justify-end' : 'justify-start');
    d.innerHTML = `<div class="max-w-[85%] px-3.5 py-2.5 rounded-2xl text-[13px] leading-relaxed ${
        esUsuario
            ? 'bg-gray-900 text-white rounded-br-md'
            : 'bg-gray-100 text-gray-700 rounded-bl-md'
    }">${texto}</div>`;
    c.appendChild(d);
    c.scrollTop = c.scrollHeight;
}

function enviarSugerencia(btn) {
    document.getElementById('chat-welcome')?.remove();
    document.getElementById('sugerencias')?.remove();
    procesarMensaje(btn.textContent.trim());
}

function enviarMensaje() {
    const input = document.getElementById('chat-input');
    const texto = input.value.trim();
    if (!texto) return;
    input.value = '';
    document.getElementById('chat-welcome')?.remove();
    document.getElementById('sugerencias')?.remove();
    procesarMensaje(texto);
}

function procesarMensaje(texto) {
    agregarMensaje(texto, true);

    const c = document.getElementById('chat-messages');
    const t = document.createElement('div');
    t.id = 'typing';
    t.className = 'chat-msg flex justify-start';
    t.innerHTML = '<div class="bg-gray-100 text-gray-500 px-3.5 py-2.5 rounded-2xl rounded-bl-md"><span class="typing-dots"><span></span> <span></span> <span></span></span></div>';
    c.appendChild(t);
    c.scrollTop = c.scrollHeight;

    fetch('{{ route("alumno.chatbot") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ mensaje: texto })
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('typing')?.remove();
        agregarMensaje(data.respuesta ?? 'Sin respuesta.', false);
    })
    .catch(() => {
        document.getElementById('typing')?.remove();
        agregarMensaje('Error al contactar al asistente.', false);
    });
}
</script>
@endpush

</x-panel>
