<x-panel title="Cambiar Contrasena" panelNombre="{{ $panel['nombre'] }}">
    @if($panel['nav'])
        <x-slot name="nav">@include($panel['nav'])</x-slot>
    @endif

    <div class="max-w-lg mx-auto space-y-5">

        <div class="text-center">
            <div
                class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center mb-4 shadow-lg shadow-blue-200">
                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-[22px] font-bold text-gray-900">Cambiar Contrasena</h1>
            <p class="text-[13px] text-gray-400 mt-1">Asegurate de usar una contrasena segura de al menos 8 caracteres.
            </p>
        </div>

        @if(session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-[13px] flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('cambiar-password.update') }}"
            class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
            @csrf
            @method('PUT')

            {{-- Contrasena actual --}}
            <div>
                <label class="text-[12px] font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Contrasena
                    actual</label>
                <div class="relative">
                    <input type="password" name="current_password" required id="current_password" placeholder="••••••••"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-[14px] text-gray-800 focus:ring-2 focus:ring-sky-300 focus:border-sky-400 outline-none @error('current_password') border-red-300 @enderror">
                </div>
                @error('current_password')
                    <p class="text-red-500 text-[11px] mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <hr class="border-gray-100">

            {{-- Nueva contrasena --}}
            <div>
                <label class="text-[12px] font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Nueva
                    contrasena</label>
                <input type="password" name="password" required id="new_password" placeholder="Minimo 8 caracteres"
                    oninput="checkPasswordStrength(this.value)"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-[14px] text-gray-800 focus:ring-2 focus:ring-sky-300 focus:border-sky-400 outline-none @error('password') border-red-300 @enderror">

                {{-- Indicador de fuerza --}}
                <div class="mt-2 flex gap-1.5" id="strength-bars">
                    <div class="h-1 flex-1 rounded-full bg-gray-100 transition-colors" id="bar1"></div>
                    <div class="h-1 flex-1 rounded-full bg-gray-100 transition-colors" id="bar2"></div>
                    <div class="h-1 flex-1 rounded-full bg-gray-100 transition-colors" id="bar3"></div>
                    <div class="h-1 flex-1 rounded-full bg-gray-100 transition-colors" id="bar4"></div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1" id="strength-text"></p>

                @error('password')
                    <p class="text-red-500 text-[11px] mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirmar --}}
            <div>
                <label class="text-[12px] font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Confirmar
                    nueva contrasena</label>
                <input type="password" name="password_confirmation" required placeholder="Repite la nueva contrasena"
                    oninput="checkMatch()" id="password_confirmation"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-[14px] text-gray-800 focus:ring-2 focus:ring-sky-300 focus:border-sky-400 outline-none">
                <p class="text-[11px] mt-1" id="match-text"></p>
            </div>

            <button type="submit"
                class="w-full bg-[#0606F0] hover:bg-[#04276B] text-white font-semibold py-3 rounded-xl transition-colors text-[14px] active:scale-[0.98]">
                Actualizar contraseña
            </button>
        </form>

    </div>

    @push('scripts')
        <script>
            function checkPasswordStrength(pwd) {
                let score = 0;
                if (pwd.length >= 8) score++;
                if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) score++;
                if (/\d/.test(pwd)) score++;
                if (/[^a-zA-Z0-9]/.test(pwd)) score++;

                const colors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-emerald-400'];
                const texts = ['Muy debil', 'Debil', 'Buena', 'Excelente'];

                for (let i = 1; i <= 4; i++) {
                    const bar = document.getElementById('bar' + i);
                    bar.className = 'h-1 flex-1 rounded-full transition-colors ' + (i <= score ? colors[score - 1] : 'bg-gray-100');
                }

                document.getElementById('strength-text').textContent = pwd.length > 0 ? texts[Math.max(0, score - 1)] : '';
                document.getElementById('strength-text').className = 'text-[11px] mt-1 ' + (score >= 3 ? 'text-emerald-500' : score >= 2 ? 'text-yellow-500' : 'text-red-400');
            }

            function checkMatch() {
                const pwd = document.getElementById('new_password').value;
                const confirm = document.getElementById('password_confirmation').value;
                const el = document.getElementById('match-text');

                if (confirm.length === 0) { el.textContent = ''; return; }

                if (pwd === confirm) {
                    el.textContent = 'Las contrasenas coinciden';
                    el.className = 'text-[11px] mt-1 text-emerald-500';
                } else {
                    el.textContent = 'Las contrasenas no coinciden';
                    el.className = 'text-[11px] mt-1 text-red-400';
                }
            }
        </script>
    @endpush

</x-panel>