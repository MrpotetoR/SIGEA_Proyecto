<x-panel title="Registrar Servicio Social" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="max-w-2xl space-y-5">

    <h1 class="text-[22px] font-bold text-gray-900">Registrar Servicio Social</h1>

    <form method="POST" action="{{ route('docente.servicio-social.store') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
        @csrf

        <div>
            <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block">Alumno</label>
            <select name="id_alumno" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-violet-300 outline-none">
                <option value="">Selecciona alumno</option>
                @foreach(\App\Models\Alumno::activos()->orderBy('apellidos')->get() as $a)
                    <option value="{{ $a->id_alumno }}">{{ $a->nombre_completo }} ({{ $a->matricula }})</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block">Horas Acumuladas</label>
                <input type="number" name="horas_acumuladas" min="0" step="1" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-violet-300 outline-none">
            </div>
            <div>
                <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block">Estatus</label>
                <select name="estatus" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-violet-300 outline-none">
                    <option value="en_curso">En curso</option>
                    <option value="completado">Completado</option>
                </select>
            </div>
        </div>

        <div>
            <label class="text-[11px] font-semibold text-gray-500 uppercase mb-1 block">Institucion</label>
            <input type="text" name="institucion" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] focus:ring-2 focus:ring-violet-300 outline-none" placeholder="Nombre de la institucion...">
        </div>

        <div class="flex justify-between items-center pt-2">
            <a href="{{ route('docente.servicio-social.index') }}" class="text-[12px] text-gray-500 hover:text-gray-700">&larr; Volver</a>
            <button type="submit" class="bg-gray-900 text-white px-6 py-2.5 rounded-xl text-[13px] font-medium hover:bg-gray-700 transition-colors">Guardar</button>
        </div>
    </form>

</div>

</x-panel>
