<x-panel title="Horas ACUDE" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <div class="flex items-center justify-between">
        <h1 class="text-[22px] font-bold text-gray-900">Horas ACUDE</h1>
        <a href="{{ route('docente.horas-culturales.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded-xl text-[12px] font-medium hover:bg-gray-700 transition-colors">+ Registrar Horas</a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-[13px]">{{ session('success') }}</div>
    @endif

    @if($registros->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">Alumno</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase">Tipo</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase">Horas</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">Descripcion</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($registros as $reg)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-[13px] font-medium text-gray-800">{{ $reg->alumno?->nombre_completo ?? '---' }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $reg->tipo === 'cultural' ? 'bg-indigo-50 text-indigo-600' : 'bg-emerald-50 text-emerald-600' }}">
                                    {{ ucfirst($reg->tipo) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-[13px] text-center font-bold text-gray-700">{{ $reg->horas_acumuladas }}</td>
                            <td class="px-5 py-3 text-[12px] text-gray-500 max-w-xs truncate">{{ $reg->descripcion ?? '---' }}</td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('docente.horas-culturales.edit', $reg->id_registro) }}" class="text-[11px] text-violet-600 hover:text-violet-800 px-2 py-1 rounded-lg hover:bg-violet-50">Editar</a>
                                    <form method="POST" action="{{ route('docente.horas-culturales.destroy', $reg->id_registro) }}" onsubmit="return confirm('Eliminar?')">
                                        @csrf @method('DELETE')
                                        <button class="text-[11px] text-red-500 hover:text-red-700 px-2 py-1 rounded-lg hover:bg-red-50">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-[14px] text-gray-400">Sin registros de horas ACUDE.</p>
        </div>
    @endif

</div>

</x-panel>
