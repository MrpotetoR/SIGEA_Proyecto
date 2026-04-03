<x-panel title="Editar Horario" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    <div class="mb-5">
        <a href="{{ route('director.horarios.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver a Horarios
        </a>
    </div>

    <div class="max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-[15px] font-semibold text-gray-800 dark:text-gray-200 mb-5">Editar Horario #{{ $horario->id_horario }}</h3>

        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl text-sm mb-5">
                <ul class="list-disc list-inside">@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('director.horarios.update', $horario->id_horario) }}" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Grupo</label>
                    <input type="text" value="{{ $horario->grupo?->clave_grupo ?? 'N/A' }}" disabled class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Materia</label>
                    <input type="text" value="{{ $horario->materia?->nombre_materia ?? 'N/A' }}" disabled class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                </div>
            </div>
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Docente *</label>
                <select name="id_docente" required class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                    <option value="">Seleccionar...</option>
                    @foreach($docentes as $d)
                        <option value="{{ $d->id_docente }}" {{ old('id_docente', $horario->id_docente) == $d->id_docente ? 'selected' : '' }}>{{ $d->nombre_completo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Dia *</label>
                    <select name="dia_semana" required class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                        @foreach(['lunes','martes','miercoles','jueves','viernes','sabado'] as $dia)
                            <option value="{{ $dia }}" {{ old('dia_semana', $horario->dia_semana) == $dia ? 'selected' : '' }}>{{ ucfirst($dia) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Hora Inicio *</label>
                    <input type="time" name="hora_inicio" value="{{ old('hora_inicio', \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i')) }}" required class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Hora Fin *</label>
                    <input type="time" name="hora_fin" value="{{ old('hora_fin', \Carbon\Carbon::parse($horario->hora_fin)->format('H:i')) }}" required class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-[#0606F0] dark:bg-[#0606F0] text-white text-sm font-medium rounded-xl hover:bg-[#04276B] dark:hover:bg-blue-400 transition-colors">Guardar Cambios</button>
                <a href="{{ route('director.horarios.index') }}" class="px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Cancelar</a>
            </div>
        </form>
    </div>
</x-panel>
