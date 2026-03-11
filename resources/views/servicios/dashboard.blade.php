<x-panel
    title="Dashboard Servicios Escolares"
    panelNombre="Servicios Escolares">

    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <p class="text-3xl font-bold text-indigo-700">{{ $stats['total_alumnos'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Alumnos Activos</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['bajas_temporales'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Bajas Temporales</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <p class="text-3xl font-bold text-indigo-700">{{ $stats['total_docentes'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Docentes</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <p class="text-3xl font-bold text-indigo-700">{{ $stats['total_carreras'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Carreras</p>
        </div>
    </div>

    {{-- Ciclo activo --}}
    @if($stats['ciclo_activo'])
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Ciclo Escolar Activo</h2>
            <div class="flex items-center gap-6">
                <div>
                    <p class="text-2xl font-bold text-indigo-700">{{ $stats['ciclo_activo']->nombre }}</p>
                </div>
                <div class="text-sm text-gray-500">
                    <p>Inicio: {{ $stats['ciclo_activo']->fecha_inicio->format('d/m/Y') }}</p>
                    <p>Fin: {{ $stats['ciclo_activo']->fecha_fin->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-300 rounded-xl p-4 mb-6">
            <p class="text-yellow-700 font-medium">⚠ No hay un ciclo escolar activo.
                <a href="{{ route('servicios.ciclos.create') }}" class="underline">Crear uno →</a>
            </p>
        </div>
    @endif

    {{-- Accesos rápidos --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Accesos Rápidos</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('servicios.alumnos.create') }}"
               class="block text-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg py-3 px-4 text-sm font-medium transition-colors">
                ➕ Nuevo Alumno
            </a>
            <a href="{{ route('servicios.docentes.create') }}"
               class="block text-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg py-3 px-4 text-sm font-medium transition-colors">
                ➕ Nuevo Docente
            </a>
            <a href="{{ route('servicios.noticias.create') }}"
               class="block text-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg py-3 px-4 text-sm font-medium transition-colors">
                📰 Publicar Noticia
            </a>
            <a href="{{ route('servicios.reportes') }}"
               class="block text-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg py-3 px-4 text-sm font-medium transition-colors">
                📊 Ver Reportes
            </a>
        </div>
    </div>

</x-panel>
