<x-panel title="Editar Horas ACUDE" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="max-w-2xl space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900">Editar Horas ACUDE</h1>

    <form method="POST" action="{{ route('docente.horas-culturales.update', $horasCultural->id_registro) }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block">Alumno</label>
            <p class="text-[14px] font-medium text-gray-800 bg-gray-50 rounded-xl px-3 py-2.5">{{ $horasCultural->alumno?->nombre_completo }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block">Tipo</label>
                <select name="tipo" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-violet-300 outline-none">
                    <option value="cultural" {{ $horasCultural->tipo === 'cultural' ? 'selected' : '' }}>Cultural</option>
                    <option value="deportiva" {{ $horasCultural->tipo === 'deportiva' ? 'selected' : '' }}>Deportiva</option>
                </select>
            </div>
            <div>
                <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block">Horas</label>
                <input type="number" name="horas_acumuladas" value="{{ $horasCultural->horas_acumuladas }}" min="0.5" max="100" step="0.5" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-violet-300 outline-none">
            </div>
        </div>

        <div>
            <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block">Descripcion</label>
            <textarea name="descripcion" rows="3" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-violet-300 outline-none resize-none">{{ $horasCultural->descripcion }}</textarea>
        </div>

        <div class="flex justify-between items-center pt-2">
            <a href="{{ route('docente.horas-culturales.index') }}" class="text-[12px] text-gray-500 hover:text-gray-700">&larr; Volver</a>
            <button type="submit" class="bg-gray-900 text-white px-6 py-2.5 rounded-xl text-[13px] font-medium hover:bg-gray-700 transition-colors">Actualizar</button>
        </div>
    </form>

</div>

</x-panel>
