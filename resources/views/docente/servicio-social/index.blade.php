<x-panel title="Servicio Social" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

<div class="space-y-5">

    <div class="flex items-center justify-between">
        <h1 class="text-[22px] font-bold text-gray-900">Servicio Social</h1>
        <a href="{{ route('docente.servicio-social.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded-xl text-[12px] font-medium hover:bg-gray-700 transition-colors">+ Registrar</a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-[13px]">{{ session('success') }}</div>
    @endif

    @if($alumnos->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">Alumno</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase">Estatus</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase">Horas</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($alumnos as $alumno)
                        @php $ss = $alumno->servicioSocial; @endphp
                        @if($ss)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-5 py-3 text-[13px] font-medium text-gray-800">{{ $alumno->nombre_completo }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $ss->estatus === 'completado' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                        {{ ucfirst($ss->estatus ?? 'pendiente') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-[13px] text-center font-bold text-gray-700">{{ $ss->horas_acumuladas ?? 0 }}</td>
                                <td class="px-5 py-3 text-center">
                                    <a href="{{ route('docente.servicio-social.edit', $ss->id_servicio) }}" class="text-[11px] text-violet-600 hover:text-violet-800 px-2 py-1 rounded-lg hover:bg-violet-50">Editar</a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-[14px] text-gray-400">Sin registros de servicio social.</p>
        </div>
    @endif

</div>

</x-panel>
