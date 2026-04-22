<x-guest-layout>

    {{-- Encabezado --}}
    <div class="text-center mb-7">
        <h2 class="text-[24px] font-extrabold text-gray-900 slide-up">Codigo de verificacion</h2>
        <p class="text-[13px] text-gray-400 mt-1.5 slide-up" style="animation-delay: 0.12s;">
            Enviamos un codigo de 6 digitos a <strong class="text-gray-600">{{ $email }}</strong>.
            Ingresalo para continuar.
        </p>
    </div>

    @if(session('status'))
        <div class="mb-5 bg-emerald-50 border border-emerald-100 text-emerald-600 px-4 py-3 rounded-2xl text-[13px] flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-2xl text-[13px] flex items-center gap-2 shake">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.verify-code.store') }}" class="space-y-5"
          x-data="{
              digits: ['', '', '', '', '', ''],
              get codigo() { return this.digits.join(''); },
              handleInput(index, event) {
                  const value = event.target.value.replace(/\D/g, '').slice(-1);
                  this.digits[index] = value;
                  event.target.value = value;
                  if (value && index < 5) {
                      this.$refs['d' + (index + 1)].focus();
                  }
              },
              handleKeydown(index, event) {
                  if (event.key === 'Backspace' && !this.digits[index] && index > 0) {
                      this.$refs['d' + (index - 1)].focus();
                  }
                  if (event.key === 'ArrowLeft' && index > 0) {
                      this.$refs['d' + (index - 1)].focus();
                  }
                  if (event.key === 'ArrowRight' && index < 5) {
                      this.$refs['d' + (index + 1)].focus();
                  }
              },
              handlePaste(event) {
                  event.preventDefault();
                  const pasted = (event.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                  for (let i = 0; i < 6; i++) {
                      this.digits[i] = pasted[i] || '';
                      if (this.$refs['d' + i]) this.$refs['d' + i].value = pasted[i] || '';
                  }
                  const next = pasted.length >= 6 ? 5 : pasted.length;
                  if (this.$refs['d' + next]) this.$refs['d' + next].focus();
              }
          }">
        @csrf

        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="codigo" :value="codigo">

        <div class="slide-up" style="animation-delay: 0.15s;">
            <label class="block text-[12px] font-semibold text-gray-500 uppercase tracking-wider mb-3 text-center">
                Codigo de 6 digitos
            </label>
            <div class="flex justify-center gap-1.5 xs:gap-2 sm:gap-3" @paste="handlePaste">
                @for($i = 0; $i < 6; $i++)
                    <input
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="1"
                        x-ref="d{{ $i }}"
                        @input="handleInput({{ $i }}, $event)"
                        @keydown="handleKeydown({{ $i }}, $event)"
                        @if($i === 0) autofocus @endif
                        class="w-10 h-11 sm:w-12 sm:h-14 text-center text-[18px] sm:text-[22px] font-bold text-gray-800 bg-gray-50/80 border border-gray-200 rounded-xl outline-none focus:border-[#0606F0] focus:bg-white focus:ring-2 focus:ring-[#0606F0]/10 transition-all min-w-0 flex-shrink">
                @endfor
            </div>
            <p class="text-[11px] text-gray-400 text-center mt-3">
                El codigo expira en 15 minutos.
            </p>
        </div>

        <div class="slide-up" style="animation-delay: 0.2s;">
            <button type="submit"
                    :disabled="codigo.length !== 6"
                    :class="codigo.length === 6 ? 'bg-[#0606F0] hover:bg-[#04276B]' : 'bg-gray-300 cursor-not-allowed'"
                    class="btn-primary w-full text-white font-semibold py-3.5 rounded-xl text-[14px] shadow-lg shadow-gray-900/10 transition-colors">
                Verificar codigo
            </button>
        </div>
    </form>

    {{-- Reenviar codigo --}}
    <form method="POST" action="{{ route('password.verify-code.resend') }}" class="text-center mt-4 slide-up" style="animation-delay: 0.23s;">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <button type="submit" class="text-[12px] text-[#0606F0] hover:text-[#04276B] font-semibold transition-colors">
            Reenviar codigo
        </button>
    </form>

    <div class="text-center mt-3 slide-up" style="animation-delay: 0.25s;">
        <a href="{{ route('password.request') }}" class="text-[12px] text-gray-400 hover:text-gray-600 transition-colors font-medium">
            &larr; Cambiar correo
        </a>
    </div>

</x-guest-layout>
