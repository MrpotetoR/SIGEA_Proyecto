<x-guest-layout>

    {{-- Greeting --}}
    <div class="text-center mb-7">
        <h2 class="text-[24px] font-extrabold text-gray-900 slide-up">Bienvenido!</h2>
        <p class="text-[13px] text-gray-400 mt-1.5 slide-up" style="animation-delay: 0.12s;">Ingresa tus credenciales institucionales</p>
    </div>

    {{-- Error alert --}}
    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-2xl text-[13px] flex items-center gap-2 shake">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('status'))
        <div class="mb-5 bg-emerald-50 border border-emerald-100 text-emerald-600 px-4 py-3 rounded-2xl text-[13px] flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5" id="loginForm">
        @csrf

        {{-- Email --}}
        <div class="slide-up" style="animation-delay: 0.15s;">
            <label for="email" class="block text-[12px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Correo electronico</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-[18px] h-[18px] text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       required autofocus autocomplete="username"
                       placeholder="correo@institucion.edu.mx"
                       class="input-field w-full bg-gray-50/80 border border-gray-200 rounded-xl pl-12 pr-4 py-3.5 text-[14px] text-gray-800 placeholder-gray-300 outline-none hover:border-gray-300">
            </div>
        </div>

        {{-- Password --}}
        <div class="slide-up" style="animation-delay: 0.2s;">
            <label for="password" class="block text-[12px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Contrasena</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-[18px] h-[18px] text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input id="password" type="password" name="password"
                       required autocomplete="current-password"
                       placeholder="••••••••"
                       class="input-field w-full bg-gray-50/80 border border-gray-200 rounded-xl pl-12 pr-12 py-3.5 text-[14px] text-gray-800 placeholder-gray-300 outline-none hover:border-gray-300">
                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <svg id="eye-off" class="w-[18px] h-[18px] text-gray-300 hover:text-gray-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                    <svg id="eye-on" class="w-[18px] h-[18px] text-gray-300 hover:text-gray-500 transition-colors hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Remember & Forgot --}}
        <div class="flex items-center justify-between slide-up" style="animation-delay: 0.25s;">
            <label class="flex items-center gap-2.5 cursor-pointer group">
                <input type="checkbox" name="remember"
                       class="w-4 h-4 rounded-md border-gray-300 bg-gray-50 text-indigo-600 focus:ring-indigo-500/30 focus:ring-offset-0">
                <span class="text-[12px] text-gray-400 group-hover:text-gray-600 transition-colors">Recordarme</span>
            </label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-[12px] text-red-400 hover:text-red-500 font-medium transition-colors">
                    Olvidaste tu contrasena?
                </a>
            @endif
        </div>

        {{-- Divider --}}
        <div class="flex items-center gap-3 slide-up" style="animation-delay: 0.28s;">
            <div class="flex-1 h-px bg-gray-100"></div>
        </div>

        {{-- Login button --}}
        <div class="slide-up" style="animation-delay: 0.3s;">
            <button type="submit" id="btnLogin"
                    class="btn-primary w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3.5 rounded-xl text-[14px] shadow-lg shadow-gray-900/10">
                <span id="btnText">Iniciar sesion</span>
                <svg id="btnSpinner" class="hidden animate-spin mx-auto w-5 h-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </button>
        </div>
    </form>

    <script>
    function togglePassword() {
        const input = document.getElementById('password');
        const eyeOff = document.getElementById('eye-off');
        const eyeOn = document.getElementById('eye-on');
        if (input.type === 'password') {
            input.type = 'text';
            eyeOff.classList.add('hidden');
            eyeOn.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeOff.classList.remove('hidden');
            eyeOn.classList.add('hidden');
        }
    }

    document.getElementById('loginForm').addEventListener('submit', function() {
        document.getElementById('btnText').classList.add('hidden');
        document.getElementById('btnSpinner').classList.remove('hidden');
        document.getElementById('btnLogin').disabled = true;
        document.getElementById('btnLogin').classList.add('opacity-80');
    });
    </script>

</x-guest-layout>
