<x-panel title="Editar Administrador" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    <div class="max-w-xl">
        <a href="{{ route('admin.administradores.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-4 inline-block">← Volver</a>

        @if($errors->any())
            <div class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.administradores.update', $admin) }}" class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre completo *</label>
                <input type="text" name="name" value="{{ old('name', $admin->name) }}" required maxlength="150"
                       pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                       oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correo electrónico *</label>
                <input type="email" name="email" value="{{ old('email', $admin->email) }}" required maxlength="255"
                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
            </div>

            <div class="border-t dark:border-gray-700 pt-5">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Cambiar contraseña (opcional — déjalo en blanco para conservar la actual):</p>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nueva contraseña</label>
                        <input type="password" name="password" minlength="8"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar nueva contraseña</label>
                        <input type="password" name="password_confirmation" minlength="8"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="bg-[#0606F0] hover:bg-[#04276B] text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">Guardar cambios</button>
                <a href="{{ route('admin.administradores.index') }}" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
            </div>
        </form>
    </div>
</x-panel>
