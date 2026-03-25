<x-guest-layout>

    {{-- Heading --}}
    <h1
        style="font-size: 42px; font-weight: 300; color: #fff; letter-spacing: -0.02em; margin-bottom: 36px; line-height: 1;">
        Iniciar Sesión
    </h1>

    {{-- Error alert --}}
    @if($errors->any())
        <div class="shake"
            style="margin-bottom: 20px; font-size: 12px; color: #f87171; border-bottom: 1px solid rgba(248,113,113,0.3); padding-bottom: 12px;">
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('status'))
        <div
            style="margin-bottom: 20px; font-size: 12px; color: #34d399; border-bottom: 1px solid rgba(52,211,153,0.3); padding-bottom: 12px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        {{-- Email --}}
        <div style="margin-bottom: 28px;">
            <label for="email" class="field-label">Correo Electronico </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="username" placeholder="correo@institucion.edu.mx" class="line-input">
        </div>

        {{-- Password --}}
        <div style="margin-bottom: 28px; position: relative;">
            <label for="password" class="field-label">Password</label>
            <div style="position: relative;">
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    placeholder="••••••••••" class="line-input" style="padding-right: 28px;">
                <button type="button" onclick="togglePassword()" class="pass-toggle" tabindex="-1">
                    <svg id="eye-off" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                    <svg id="eye-on" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Remember & Forgot --}}
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 44px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="remember"
                    style="width: 14px; height: 14px; accent-color: #fff; cursor: pointer;">
                <span style="font-size: 12px; color: rgba(255,255,255,0.4);">Recuerdame</span>
            </label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                    style="font-size: 12px; color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.15s;"
                    onmouseover="this.style.color='rgba(255,255,255,0.8)'"
                    onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        {{-- Sign In button — aligned right --}}
        <div style="display: flex; justify-content: flex-end;">
            <button type="submit" class="signin-btn" id="btnLogin">
                <span id="btnText">Entrar</span>
                <svg id="btnSpinner" style="display:none;" class="animate-spin" width="20" height="20"
                    viewBox="0 0 24 24" fill="none">
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
                eyeOff.style.display = 'none';
                eyeOn.style.display = 'block';
            } else {
                input.type = 'password';
                eyeOff.style.display = 'block';
                eyeOn.style.display = 'none';
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function () {
            document.getElementById('btnText').style.display = 'none';
            document.getElementById('btnSpinner').style.display = 'block';
            document.getElementById('btnLogin').disabled = true;
            document.getElementById('btnLogin').style.opacity = '0.7';
        });
    </script>

</x-guest-layout>