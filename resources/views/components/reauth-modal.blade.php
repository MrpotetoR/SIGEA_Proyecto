{{--
    Modal reutilizable para re-autenticación por contraseña.

    Uso desde JS:
        window.dispatchEvent(new CustomEvent('reauth:open', {
            detail: {
                action: 'crear_carrera' | 'asignar_carrera' | ...,
                title:  'Confirmar creación de carrera',
                description: 'Por seguridad, ingresa tu contraseña antes de continuar.',
                onSuccess: () => { ... },   // callback al verificar OK
                onCancel:  () => { ... },   // opcional
            }
        }));

    El componente emite el evento 'reauth:success' (con detail.action) al
    completar verificación exitosa. Puedes escucharlo globalmente.
--}}
<div
    x-data="reauthModal()"
    x-on:reauth:open.window="abrir($event.detail)"
    x-on:keydown.escape.window="cerrar()">
    <template x-teleport="body">
<div
    x-show="visible"
    x-cloak
    class="fixed inset-0 z-[200] flex items-center justify-center p-4"
    style="display: none;">

    {{-- Fondo --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="cerrar()"></div>

    {{-- Card --}}
    <div
        x-show="visible"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-gray-700">

        {{-- Header --}}
        <div class="px-6 pt-6 pb-4 border-b dark:border-gray-700">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100" x-text="title"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5" x-text="description"></p>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <form @submit.prevent="enviar()" class="px-6 py-5 space-y-4">
            {{-- Modo sesión expirada (cookies borradas, CSRF desincronizado, etc.) --}}
            <template x-if="sesionExpirada">
                <div class="text-center py-2">
                    <div class="mx-auto w-14 h-14 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tu sesión expiró</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-xs mx-auto">
                        Tu sesión se cerró (probablemente por borrar cookies o por inactividad).
                        Recarga la página para iniciar sesión de nuevo.
                    </p>
                    <button type="button" @click="recargarPagina()"
                            class="mt-4 bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors">
                        Recargar página
                    </button>
                </div>
            </template>

            {{-- Modo normal: input de contraseña --}}
            <template x-if="!bloqueado && !sesionExpirada">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Contraseña de administrador
                    </label>
                    <input
                        type="password"
                        x-model="password"
                        x-ref="passwordInput"
                        :disabled="cargando"
                        autocomplete="current-password"
                        class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none disabled:opacity-60"
                        placeholder="••••••••">

                    {{-- Mensaje (solo en modo normal) --}}
                    <div x-show="mensaje" x-cloak class="mt-3 rounded-lg px-3 py-2 text-sm bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800">
                        <span x-text="mensaje"></span>
                    </div>

                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">
                        Máximo 3 intentos. Tras 3 fallos, deberás esperar 5 minutos.
                    </p>
                </div>
            </template>

            {{-- Modo bloqueado: contador regresivo --}}
            <template x-if="bloqueado && !sesionExpirada">
                <div class="text-center py-2">
                    <div class="mx-auto w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Demasiados intentos fallidos</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Por seguridad, debes esperar antes de volver a intentar.</p>

                    <div class="mt-4 inline-block bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl px-6 py-3">
                        <p class="text-[10px] uppercase tracking-wider text-red-600 dark:text-red-400 font-semibold">Tiempo restante</p>
                        <p class="text-3xl font-mono font-bold text-red-700 dark:text-red-300 mt-0.5 tabular-nums" x-text="tiempoFormateado"></p>
                    </div>
                </div>
            </template>

            <div class="flex items-center justify-end gap-2 pt-2 border-t dark:border-gray-700 -mx-6 px-6"
                 x-show="!sesionExpirada">
                <button type="button" @click="cerrar()"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <span x-show="!bloqueado">Cancelar</span>
                    <span x-show="bloqueado">Cerrar</span>
                </button>
                <button type="submit"
                        x-show="!bloqueado"
                        :disabled="cargando || !password"
                        class="bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-4 py-2 rounded-lg text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <span x-show="!cargando">Confirmar</span>
                    <span x-show="cargando">Verificando...</span>
                </button>
            </div>
        </form>
    </div>
</div>
    </template>
</div>

@once
    @push('scripts')
    <script>
        function reauthModal() {
            return {
                visible: false,
                action: null,
                title: '',
                description: '',
                password: '',
                cargando: false,
                bloqueado: false,
                sesionExpirada: false,
                mensaje: '',
                segundosRestantes: 0,
                tickerId: null,
                onSuccess: null,
                onCancel: null,

                recargarPagina() {
                    window.location.reload();
                },

                get tiempoFormateado() {
                    const s = Math.max(0, this.segundosRestantes);
                    const m = Math.floor(s / 60);
                    const r = s % 60;
                    return String(m).padStart(2, '0') + ':' + String(r).padStart(2, '0');
                },

                abrir(detail) {
                    this.action         = detail.action;
                    this.title          = detail.title || 'Confirmar acción';
                    this.description    = detail.description || 'Por seguridad, ingresa tu contraseña.';
                    this.onSuccess      = detail.onSuccess || null;
                    this.onCancel       = detail.onCancel  || null;
                    this.password       = '';
                    this.mensaje        = '';
                    this.bloqueado      = false;
                    this.sesionExpirada = false;
                    this.detenerTicker();
                    this.visible        = true;
                    // Verificar estado de bloqueo en el servidor (puede haber un bloqueo
                    // previo aún vigente aunque el cliente haya cerrado y reabierto).
                    this.verificarEstadoBloqueo();
                },

                async verificarEstadoBloqueo() {
                    try {
                        const url = '{{ route('admin.reauth.estado') }}?action=' + encodeURIComponent(this.action);
                        const res = await fetch(url, {
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });
                        if (res.status === 419 || res.status === 401) {
                            this.sesionExpirada = true;
                            return;
                        }
                        if (!res.ok) {
                            this.$nextTick(() => this.$refs.passwordInput?.focus());
                            return;
                        }
                        const data = await res.json();
                        if (data.bloqueado) {
                            this.bloqueado = true;
                            this.iniciarTicker(data.segundos_espera || 1);
                        } else {
                            this.$nextTick(() => this.$refs.passwordInput?.focus());
                        }
                    } catch (e) {
                        this.$nextTick(() => this.$refs.passwordInput?.focus());
                    }
                },

                cerrar() {
                    if (this.cargando) return;
                    this.visible = false;
                    this.detenerTicker();
                    if (typeof this.onCancel === 'function') this.onCancel();
                },

                iniciarTicker(segundos) {
                    this.segundosRestantes = Math.max(1, parseInt(segundos, 10) || 0);
                    this.detenerTicker();
                    this.tickerId = setInterval(() => {
                        this.segundosRestantes--;
                        if (this.segundosRestantes <= 0) {
                            this.detenerTicker();
                            this.bloqueado = false;
                            this.mensaje   = '';
                            this.password  = '';
                            this.$nextTick(() => this.$refs.passwordInput?.focus());
                        }
                    }, 1000);
                },

                detenerTicker() {
                    if (this.tickerId) {
                        clearInterval(this.tickerId);
                        this.tickerId = null;
                    }
                },

                /** Lee el token CSRF actual (prioriza cookie XSRF-TOKEN, que Laravel mantiene fresco). */
                getCsrfToken() {
                    const xsrf = document.cookie
                        .split('; ')
                        .find(c => c.startsWith('XSRF-TOKEN='));
                    if (xsrf) {
                        return decodeURIComponent(xsrf.split('=')[1]);
                    }
                    return document.querySelector('meta[name="csrf-token"]')?.content || '';
                },

                async enviar() {
                    if (this.cargando || this.bloqueado || this.sesionExpirada || !this.password) return;

                    this.cargando = true;
                    this.mensaje  = '';

                    try {
                        const res = await fetch('{{ route('admin.reauth') }}', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-XSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({
                                password: this.password,
                                action:   this.action,
                            }),
                        });

                        // Sesión expirada (CSRF desincronizado o sesión cerrada por borrado de cookies).
                        if (res.status === 419 || res.status === 401) {
                            this.password       = '';
                            this.sesionExpirada = true;
                            return;
                        }

                        const data = await res.json();

                        if (res.ok && data.success) {
                            this.visible = false;
                            this.password = '';
                            if (typeof this.onSuccess === 'function') this.onSuccess(data);
                            window.dispatchEvent(new CustomEvent('reauth:success', {
                                detail: { action: this.action },
                            }));
                            return;
                        }

                        this.password  = '';
                        this.bloqueado = !!data.bloqueado;

                        if (this.bloqueado) {
                            this.mensaje = '';
                            this.iniciarTicker(data.segundos_espera || 300);
                        } else {
                            this.mensaje = data.message || 'No se pudo verificar.';
                            this.$nextTick(() => this.$refs.passwordInput?.focus());
                        }
                    } catch (e) {
                        this.mensaje = 'Error de conexión. Intenta de nuevo.';
                    } finally {
                        this.cargando = false;
                    }
                },
            };
        }
    </script>
    @endpush
@endonce
