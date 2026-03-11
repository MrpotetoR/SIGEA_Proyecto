<x-sidebar-link href="{{ route('servicios.dashboard') }}">📊 Dashboard</x-sidebar-link>

<p class="pt-3 pb-1 px-3 text-xs text-indigo-400 uppercase tracking-widest">Alumnos</p>
<x-sidebar-link href="{{ route('servicios.alumnos.index') }}">🎓 Alumnos</x-sidebar-link>
<x-sidebar-link href="{{ route('servicios.inscripciones') }}">📋 Inscripciones</x-sidebar-link>
<x-sidebar-link href="{{ route('servicios.constancias') }}">📄 Constancias</x-sidebar-link>

<p class="pt-3 pb-1 px-3 text-xs text-indigo-400 uppercase tracking-widest">Académico</p>
<x-sidebar-link href="{{ route('servicios.docentes.index') }}">👨‍🏫 Docentes</x-sidebar-link>
<x-sidebar-link href="{{ route('servicios.carreras.index') }}">🏫 Carreras</x-sidebar-link>
<x-sidebar-link href="{{ route('servicios.materias.index') }}">📚 Materias</x-sidebar-link>
<x-sidebar-link href="{{ route('servicios.ciclos.index') }}">📅 Ciclos Escolares</x-sidebar-link>

<p class="pt-3 pb-1 px-3 text-xs text-indigo-400 uppercase tracking-widest">Contenido</p>
<x-sidebar-link href="{{ route('servicios.noticias.index') }}">📰 Noticias</x-sidebar-link>
<x-sidebar-link href="{{ route('servicios.documentos.index') }}">📁 Documentos</x-sidebar-link>
<x-sidebar-link href="{{ route('servicios.reportes') }}">📊 Reportes</x-sidebar-link>
