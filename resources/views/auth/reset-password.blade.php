<x-guest-layout>

    {{-- Greeting --}}
    <div class="text-center mb-7">
        <h2 class="text-[24px] font-extrabold text-gray-900 slide-up">Nueva contrasena</h2>
        <p class="text-[13px] text-gray-400 mt-1.5 slide-up" style="animation-delay: 0.12s;">Ingresa tu nueva contrasena para acceder a tu cuenta.</p>
    </div>

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-2xl text-[13px] flex items-center gap-2 shake">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="slide-up" style="animation-delay: 0.15s;">
            <label class="block text-[12px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Correo electronico</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-[18px] h-[18px] text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <input type="email" name="email" value="{{ old('email', $request->email) }}" required
                       class="input-field w-full bg-gray-50/80 border border-gray-200 rounded-xl pl-12 pr-4 py-3.5 text-[14px] text-gray-800 placeholder-gray-300 outline-none hover:border-gray-300">
            </div>
        </div>

        <div class="slide-up" style="animation-delay: 0.2s;">
            <label class="block text-[12px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Nueva contrasena</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-[18px] h-[18px] text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input type="password" name="password" required autocomplete="new-password" placeholder="••••••••"
                       class="input-field w-full bg-gray-50/80 border border-gray-200 rounded-xl pl-12 pr-4 py-3.5 text-[14px] text-gray-800 placeholder-gray-300 outline-none hover:border-gray-300">
            </div>
        </div>

        <div class="slide-up" style="animation-delay: 0.25s;">
            <label class="block text-[12px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Confirmar contrasena</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-[18px] h-[18px] text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••"
                       class="input-field w-full bg-gray-50/80 border border-gray-200 rounded-xl pl-12 pr-4 py-3.5 text-[14px] text-gray-800 placeholder-gray-300 outline-none hover:border-gray-300">
            </div>
        </div>

        <div class="slide-up" style="animation-delay: 0.3s;">
            <button type="submit" class="btn-primary w-full bg-[#0606F0] hover:bg-[#04276B] text-white font-semibold py-3.5 rounded-xl text-[14px] shadow-lg shadow-gray-900/10">
                Restablecer contrasena
            </button>
        </div>
    </form>

</x-guest-layout>
