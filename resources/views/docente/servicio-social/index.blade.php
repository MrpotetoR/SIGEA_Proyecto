<x-panel title="Servicio Social" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <div class="flex items-center justify-between">
        <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Servicio Social</h1>
        <a href="{{ route('docente.servicio-social.create') }}" class="bg-[#0606F0] text-white px-4 py-2 rounded-xl text-[12px] font-medium hover:bg-[#04276B] transition-colors dark:bg-[#0606F0] dark:hover:bg-[#0606F0]">+ Registrar</a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-[13px] dark:bg-green-900/30 dark:border-green-700 dark:text-green-300">{{ session('success') }}</div>
    @endif

    @if($alumnos->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 flex flex-col min-h-0" style="max-height: calc(100vh - 220px);">
            <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0 z-10">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase dark:text-gray-400">Alumno</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase dark:text-gray-400">Estatus</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase dark:text-gray-400">Horas</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase dark:text-gray-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($alumnos as $alumno)
                        @php $ss = $alumno->servicioSocial; @endphp
                        @if($ss)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50">
                                <td class="px-5 py-3 text-[13px] font-medium text-gray-800 dark:text-gray-200">{{ $alumno->nombre_completo }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $ss->estatus === 'completado' ? 'bg-emerald-50 text-emerald-600 dark:bg-green-900/30 dark:text-green-300' : 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300' }}">
                                        {{ ucfirst($ss->estatus ?? 'pendiente') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-[13px] text-center font-bold text-gray-700 dark:text-gray-200">{{ $ss->horas_acumuladas ?? 0 }}</td>
                                <td class="px-5 py-3 text-center">
                                    <a href="{{ route('docente.servicio-social.edit', $ss->id_servicio) }}" class="text-[11px] text-sky-600 hover:text-sky-800 px-2 py-1 rounded-lg hover:bg-sky-50 dark:text-blue-400 dark:hover:bg-gray-700">Editar</a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20">
            <p class="text-[14px] text-gray-400 dark:text-gray-600">Sin registros de servicio social.</p>
        </div>
    @endif

</div>

</x-panel>
