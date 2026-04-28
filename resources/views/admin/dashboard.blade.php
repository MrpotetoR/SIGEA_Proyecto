<x-panel title="Dashboard Admin" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    <div class="space-y-6">
        {{-- KPIs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Personal S.E.</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $totalPersonal }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Administradores</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $totalAdmins }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Carreras asignadas</p>
                <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $carrerasAsignadas }} / {{ $totalCarreras }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sin asignar</p>
                <p class="text-3xl font-bold {{ $carrerasSinAsignar > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400' }} mt-1">{{ $carrerasSinAsignar }}</p>
            </div>
        </div>

        @if($sinAsignar->isNotEmpty())
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-300 mb-2">
                            Carreras pendientes de asignar a personal
                        </h3>
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach($sinAsignar as $c)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white dark:bg-gray-800 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-700">
                                    {{ $c->nombre_carrera }}
                                </span>
                            @endforeach
                        </div>
                        <a href="{{ route('admin.asignaciones.index') }}"
                           class="text-xs font-semibold text-amber-700 dark:text-amber-300 hover:underline">
                            Ir a asignar carreras &rarr;
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Personal reciente --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Personal de Servicios Escolares</h3>
                <a href="{{ route('admin.personal.index') }}" class="text-xs font-medium text-[#0606F0] dark:text-blue-400 hover:underline">Ver todos &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-2 text-left">Nombre</th>
                            <th class="px-4 py-2 text-left">Correo</th>
                            <th class="px-4 py-2 text-left">Especialidad</th>
                            <th class="px-4 py-2 text-center">Carreras</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($personalReciente as $p)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-gray-100">{{ $p->nombre_completo }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $p->user?->email }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $p->especialidad }}</td>
                                <td class="px-4 py-2 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">{{ $p->carreras->count() }} / {{ \App\Models\PersonalServiciosEscolares::MAX_CARRERAS }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Sin personal registrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-panel>
