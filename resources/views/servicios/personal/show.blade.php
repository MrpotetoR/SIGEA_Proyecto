<x-panel title="Detalle Personal" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-3xl space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('servicios.personal.index') }}" class="text-sm text-indigo-600 hover:underline">← Volver</a>
            <a href="{{ route('servicios.personal.edit', $personal) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Editar
            </a>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $personal->name }}</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">Correo electrónico</dt>
                    <dd class="font-medium">{{ $personal->email }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Estado</dt>
                    <dd>
                        @if($personal->activo)
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Activo</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Inactivo</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Rol</dt>
                    <dd class="font-medium">Servicios Escolares</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Fecha de registro</dt>
                    <dd class="font-medium">{{ $personal->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
            </dl>
        </div>
    </div>
</x-panel>
