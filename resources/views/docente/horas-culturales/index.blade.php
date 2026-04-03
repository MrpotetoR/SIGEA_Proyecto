<x-panel title="Horas ACUDE" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <div class="flex items-center justify-between">
        <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Horas ACUDE</h1>
        <a href="{{ route('docente.horas-culturales.create') }}" class="bg-[#0606F0] text-white px-4 py-2 rounded-xl text-[12px] font-medium hover:bg-[#04276B] transition-colors dark:bg-[#0606F0] dark:hover:bg-[#0606F0]">+ Registrar Horas</a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-[13px] dark:bg-green-900/30 dark:border-green-700 dark:text-green-300">{{ session('success') }}</div>
    @endif

    @if($registros->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 flex flex-col min-h-0" style="max-height: calc(100vh - 220px);">
            <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0 z-10">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase dark:text-gray-400">Alumno</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase dark:text-gray-400">Tipo</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase dark:text-gray-400">Horas</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase dark:text-gray-400">Descripcion</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase dark:text-gray-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($registros as $reg)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-3 text-[13px] font-medium text-gray-800 dark:text-gray-200">{{ $reg->alumno?->nombre_completo ?? '---' }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $reg->tipo === 'cultural' ? 'bg-blue-50 text-[#0606F0] dark:bg-blue-900/30 dark:text-blue-300' : 'bg-emerald-50 text-emerald-600 dark:bg-green-900/30 dark:text-green-300' }}">
                                    {{ ucfirst($reg->tipo) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-[13px] text-center font-bold text-gray-700 dark:text-gray-200">{{ $reg->horas_acumuladas }}</td>
                            <td class="px-5 py-3 text-[12px] text-gray-500 max-w-xs truncate dark:text-gray-400">{{ $reg->descripcion ?? '---' }}</td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('docente.horas-culturales.edit', $reg->id_registro) }}" class="text-[11px] text-sky-600 hover:text-sky-800 px-2 py-1 rounded-lg hover:bg-sky-50 dark:text-blue-400 dark:hover:bg-gray-700">Editar</a>
                                    <form method="POST" action="{{ route('docente.horas-culturales.destroy', $reg->id_registro) }}" onsubmit="return confirm('Eliminar?')">
                                        @csrf @method('DELETE')
                                        <button class="text-[11px] text-red-500 hover:text-red-700 px-2 py-1 rounded-lg hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20">
            <p class="text-[14px] text-gray-400 dark:text-gray-600">Sin registros de horas ACUDE.</p>
        </div>
    @endif

</div>

</x-panel>
