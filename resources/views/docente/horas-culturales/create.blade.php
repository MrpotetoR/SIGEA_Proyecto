<x-panel title="Registrar Horas ACUDE" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="max-w-2xl space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Registrar Horas ACUDE</h1>

    <form method="POST" action="{{ route('docente.horas-culturales.store') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4 dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20">
        @csrf

        <div>
            <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block dark:text-gray-400">Alumno</label>
            <select name="id_alumno" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-sky-300 outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                <option value="">Selecciona alumno</option>
                @forelse($alumnos as $a)
                    <option value="{{ $a->id_alumno }}" {{ old('id_alumno') == $a->id_alumno ? 'selected' : '' }}>{{ $a->nombre_completo }} ({{ $a->matricula }})</option>
                @empty
                    <option value="" disabled>No tienes alumnos asignados en tus grupos</option>
                @endforelse
            </select>
            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">Solo puedes registrar horas a alumnos de los grupos que impartes.</p>
            @error('id_alumno') <p class="text-red-500 text-[11px] mt-1 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block dark:text-gray-400">Horas</label>
            <input type="number" name="horas_acumuladas" value="{{ old('horas_acumuladas') }}" min="1" max="90" step="1" maxlength="2" oninput="if(this.value.length>2)this.value=this.value.slice(0,2)" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-sky-300 outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">Máximo 2 dígitos — tope acumulado 90 h por alumno.</p>
            @error('horas_acumuladas') <p class="text-red-500 text-[11px] mt-1 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block dark:text-gray-400">Descripción</label>
            <textarea name="descripcion" rows="3" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-sky-300 outline-none resize-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200" placeholder="¿Dónde está realizando sus horas ACUDE?">{{ old('descripcion') }}</textarea>
            @error('descripcion') <p class="text-red-500 text-[11px] mt-1 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-between items-center pt-2">
            <a href="{{ route('docente.horas-culturales.index') }}" class="text-[12px] text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">&larr; Volver</a>
            <button type="submit" class="bg-[#0606F0] text-white px-6 py-2.5 rounded-xl text-[13px] font-medium hover:bg-[#04276B] transition-colors dark:bg-[#0606F0] dark:hover:bg-[#0606F0]">Guardar</button>
        </div>
    </form>

</div>

</x-panel>
