<x-panel title="Detalle Director" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-3xl space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('servicios.directores.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline">← Volver</a>
            <a href="{{ route('servicios.directores.edit', $director) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Editar
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $director->nombre_completo }}</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Correo</dt>
                    <dd class="font-medium dark:text-gray-200">{{ $director->user?->email }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Especialidad</dt>
                    <dd class="font-medium dark:text-gray-200">{{ $director->especialidad ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Estado</dt>
                    <dd>
                        @if($director->user?->activo)
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Activo</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Inactivo</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Carrera(s) que dirige</dt>
                    <dd>
                        @if($director->carrerasDirigidas->isNotEmpty())
                            @foreach($director->carrerasDirigidas as $c)
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">{{ $c->nombre_carrera }}</span>
                            @endforeach
                        @else
                            <span class="text-gray-400 dark:text-gray-400">Sin asignar</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</x-panel>
