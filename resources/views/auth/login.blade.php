<x-guest-layout>

    {{-- Heading --}}
    <div style="margin-bottom: 32px;">
        <h1 style="font-size: 26px; font-weight: 800; color: #111827; letter-spacing: -0.025em; line-height: 1.2; margin-bottom: 6px;">
            Iniciar Sesion
        </h1>
        <p style="font-size: 13.5px; color: #6B7280;">Ingresa tus credenciales para continuar</p>
    </div>

    {{-- Error alert --}}
    @if($errors->any())
        <div class="shake"
            style="margin-bottom: 20px; font-size: 13px; color: #dc2626; background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                <circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/>
            </svg>
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('status'))
        <div style="margin-bottom: 20px; font-size: 13px; color: #059669; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        {{-- Email --}}
        <div style="margin-bottom: 20px;">
            <label for="email" class="field-label">Correo electronico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="username" placeholder="correo@institucion.edu.mx" class="login-input">
        </div>

        {{-- Password --}}
        <div style="margin-bottom: 12px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
                <label for="password" class="field-label" style="margin-bottom:0;">Contrasena</label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        style="font-size:12.5px; color:#3B6CF6; text-decoration:none; font-weight:500;"
                        onmouseover="this.style.color='#2952E3'"
                        onmouseout="this.style.color='#3B6CF6'">
                        Olvidaste tu contrasena?
                    </a>
                @endif
            </div>
            <div style="position: relative;">
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    placeholder="Ingresa tu contrasena" class="login-input" style="padding-right: 44px;">
                <button type="button" onclick="togglePassword()" class="pass-toggle" tabindex="-1">
                    <svg id="eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                    <svg id="eye-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Spacer --}}
        <div style="height: 28px;"></div>

        {{-- Login button --}}
        <button type="submit" class="login-btn" id="btnLogin">
            <span id="btnText">Iniciar sesion</span>
            <svg id="btnSpinner" class="login-spinner" width="20" height="20" viewBox="0 0 24 24" fill="none" style="display:none; margin:0 auto;">
                <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.3)" stroke-width="4"></circle>
                <path fill="white" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </button>

    </form>

    <style>
        .login-spinner { animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eyeOff = document.getElementById('eye-off');
            const eyeOn  = document.getElementById('eye-on');
            if (input.type === 'password') {
                input.type = 'text';
                eyeOff.style.display = 'none';
                eyeOn.style.display  = 'block';
            } else {
                input.type = 'password';
                eyeOff.style.display = 'block';
                eyeOn.style.display  = 'none';
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function () {
            document.getElementById('btnText').style.display    = 'none';
            document.getElementById('btnSpinner').style.display = 'block';
            document.getElementById('btnLogin').disabled        = true;
            document.getElementById('btnLogin').style.opacity   = '0.75';
        });
    </script>

</x-guest-layout>
