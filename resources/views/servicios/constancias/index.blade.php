<x-panel title="Constancias" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-lg mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">

            {{-- Icono + titulo --}}
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-blue-50 dark:bg-blue-900/30 mb-3">
                    <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Generar constancia</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Selecciona un alumno y el tipo de constancia. El PDF se descargara automaticamente.</p>
            </div>

            {{-- Formulario --}}
            <form method="POST" action="{{ route('servicios.constancias.store') }}" target="_blank" class="space-y-4">
                @csrf
                <x-ajax-select
                    name="id_alumno"
                    :url="route('ajax.alumnos')"
                    label="Alumno *"
                    placeholder="Nombre o matricula..."
                    :required="true"
                />
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Tipo de constancia *</label>
                    <select name="tipo" required
                            class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <option value="estudio">De estudio</option>
                        <option value="calificaciones">De calificaciones</option>
                        <option value="comportamiento">De comportamiento</option>
                        <option value="servicio_social">Servicio social</option>
                        <option value="cultural">Actividades culturales</option>
                    </select>
                </div>
                <button type="submit"
                        class="w-full bg-blue-700 hover:bg-blue-800 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white py-2.5 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Generar y descargar PDF
                </button>
            </form>

        </div>
    </div>
</x-panel>
