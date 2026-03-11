{{-- =============================================
     resources/views/partials/sidebar-admin.blade.php
     ============================================= --}}

{{-- Copia este contenido en el archivo correspondiente --}}

<p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Principal</p>

<a href="{{ route('admin.dashboard') }}"
   class="flex items-center px-3 py-2 text-sm rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
    Dashboard
</a>

<p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-3">Gestión</p>

<a href="#" class="flex items-center px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-gray-50">
    Alumnos
</a>
<a href="#" class="flex items-center px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-gray-50">
    Docentes
</a>
<a href="#" class="flex items-center px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-gray-50">
    Carreras
</a>
<a href="#" class="flex items-center px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-gray-50">
    Ciclos Escolares
</a>
<a href="#" class="flex items-center px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-gray-50">
    Grupos
</a>
<a href="#" class="flex items-center px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-gray-50">
    Materias
</a>
<a href="#" class="flex items-center px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-gray-50">
    Noticias
</a>

<p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-3">Trámites</p>

<a href="#" class="flex items-center px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-gray-50">
    Constancias
</a>
<a href="#" class="flex items-center px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-gray-50">
    Bajas
</a>
<a href="#" class="flex items-center px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-gray-50">
    Evaluación Docente
</a>


{{-- =============================================
     resources/views/partials/sidebar-alumno.blade.php
     ============================================= --}}

<p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Mi Portal</p>

<a href="{{ route('alumno.dashboard') }}"
   class="flex items-center px-3 py-2 text-sm rounded-md {{ request()->routeIs('alumno.dashboard') ? 'bg-blue-50 text-blue-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
    Dashboard
</a>
<a href="{{ route('alumno.perfil') }}"
   class="flex items-center px-3 py-2 text-sm rounded-md {{ request()->routeIs('alumno.perfil') ? 'bg-blue-50 text-blue-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
    Mi Perfil
</a>
<a href="{{ route('alumno.horario') }}"
   class="flex items-center px-3 py-2 text-sm rounded-md {{ request()->routeIs('alumno.horario') ? 'bg-blue-50 text-blue-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
    Horario
</a>
<a href="{{ route('alumno.calificaciones') }}"
   class="flex items-center px-3 py-2 text-sm rounded-md {{ request()->routeIs('alumno.calificaciones') ? 'bg-blue-50 text-blue-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
    Calificaciones
</a>
<a href="{{ route('alumno.kardex') }}"
   class="flex items-center px-3 py-2 text-sm rounded-md {{ request()->routeIs('alumno.kardex*') ? 'bg-blue-50 text-blue-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
    Kardex
</a>
<a href="{{ route('alumno.historial') }}"
   class="flex items-center px-3 py-2 text-sm rounded-md {{ request()->routeIs('alumno.historial') ? 'bg-blue-50 text-blue-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
    Historial Académico
</a>

<p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-3">Actividades</p>

<a href="{{ route('alumno.hrs-culturales') }}"
   class="flex items-center px-3 py-2 text-sm rounded-md {{ request()->routeIs('alumno.hrs-culturales') ? 'bg-blue-50 text-blue-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
    Horas Culturales/Deportivas
</a>
<a href="{{ route('alumno.servicio-social') }}"
   class="flex items-center px-3 py-2 text-sm rounded-md {{ request()->routeIs('alumno.servicio-social') ? 'bg-blue-50 text-blue-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
    Servicio Social
</a>
<a href="{{ route('alumno.evaluacion-docente') }}"
   class="flex items-center px-3 py-2 text-sm rounded-md {{ request()->routeIs('alumno.evaluacion-docente') ? 'bg-blue-50 text-blue-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
    Evaluación Docente
</a>
<a href="{{ route('alumno.noticias') }}"
   class="flex items-center px-3 py-2 text-sm rounded-md {{ request()->routeIs('alumno.noticias') ? 'bg-blue-50 text-blue-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
    Noticias
</a>
