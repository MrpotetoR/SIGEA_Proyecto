<x-panel title="Crear Grupo" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    <div class="mb-5">
        <a href="{{ route('director.grupos.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver a Grupos
        </a>
    </div>

    <div class="max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-[15px] font-semibold text-gray-800 dark:text-gray-200 mb-5">Nuevo Grupo</h3>

        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl text-sm mb-5">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('director.grupos.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-300 mb-1.5">Clave del Grupo *</label>
                <input type="text" name="clave_grupo" value="{{ old('clave_grupo') }}" required
                    class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400"
                    placeholder="Ej: TSU-TIC-1A">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-300 mb-1.5">Ciclo Escolar *</label>
                    <select name="id_ciclo" required class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                        <option value="">Seleccionar...</option>
                        @foreach($ciclos as $ciclo)
                            <option value="{{ $ciclo->id_ciclo }}" {{ old('id_ciclo') == $ciclo->id_ciclo ? 'selected' : '' }}>{{ $ciclo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-300 mb-1.5">Cuatrimestre *</label>
                    <select name="cuatrimestre" required class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('cuatrimestre') == $i ? 'selected' : '' }}>{{ $i }}o Cuatrimestre</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-300 mb-1.5">Tutor</label>
                <select name="id_tutor" class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                    <option value="">Sin tutor</option>
                    @foreach($docentes as $d)
                        <option value="{{ $d->id_docente }}" {{ old('id_tutor') == $d->id_docente ? 'selected' : '' }}>{{ $d->nombre_completo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-colors">Crear Grupo</button>
                <a href="{{ route('director.grupos.index') }}" class="px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Cancelar</a>
            </div>
        </form>
    </div>
</x-panel>
