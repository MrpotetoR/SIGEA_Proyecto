<x-panel title="Editar Plan" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-6">Editar Plan — {{ $plan->clave_plan }}</h2>

            <form method="POST" action="{{ route('gestor.planes-bachillerato.update', $plan) }}" class="space-y-5">
                @csrf @method('PUT')
                @include('gestor.planes-bachillerato._form', ['plan' => $plan])

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors">
                        Guardar cambios
                    </button>
                    <a href="{{ route('gestor.planes-bachillerato.index') }}" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-panel>
