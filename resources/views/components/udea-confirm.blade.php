{{--
    Modal global de confirmacion UDEA.
    ──────────────────────────────────────────────────────────────────
    Se renderiza UNA sola vez en el layout (panel.blade.php). Despues
    se usa de cualquiera de estas dos formas:

    1. DECLARATIVA (recomendada para forms / botones)
       ─────────────────────────────────────────────
       <form ... data-udea-confirm
             data-confirm-title="Eliminar carpeta"
             data-confirm-message='¿Eliminar la carpeta "Reportes"?'
             data-confirm-detail="Esta accion no se puede deshacer."
             data-confirm-variant="danger"
             data-confirm-icon="trash"
             data-confirm-button="Eliminar"
             data-confirm-cancel="Cancelar">
         ...
       </form>

       O en un boton suelto (interceptaremos el click):
       <button data-udea-confirm
               data-confirm-message="..."
               onclick="document.getElementById('mi-form').submit()">...</button>

    2. PROGRAMATICA (desde scripts inline)
       ────────────────────────────────────
       udeaConfirm({
           title: 'Promover alumnos',
           message: '¿Promover 12 alumno(s)?',
           detail: 'Esta accion creara nuevas inscripciones.',
           variant: 'primary',
           icon: 'arrow-right',
           confirmText: 'Promover',
           cancelText: 'Cancelar',
       }).then(ok => { if (ok) form.submit(); });

    Variants  : danger | warning | info | success | primary
    Iconos    : warning | trash | info | check | question | arrow-right | x-circle
                ban | clipboard
--}}
<div x-data="udeaConfirmStore()" x-init="init()" x-cloak
    x-show="open"
    @keydown.escape.window="cancel()"
    class="fixed inset-0 z-[200] flex items-center justify-center px-4"
    style="display: none;">

    {{-- Backdrop --}}
    <div x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="cancel()"
        class="absolute inset-0 bg-black/50 backdrop-blur-[2px]"></div>

    {{-- Card --}}
    <div x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 text-left"
        role="dialog" aria-modal="true" :aria-labelledby="'udea-confirm-title'">

        <div class="flex items-start gap-3 mb-4">
            {{-- Icono dinamico segun variante --}}
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                :class="iconBgClass">
                <svg class="w-5 h-5" :class="iconColorClass" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="iconPath" />
                </svg>
            </div>

            <div class="flex-1 min-w-0">
                <h3 id="udea-confirm-title"
                    class="font-semibold text-gray-900 dark:text-gray-100 mb-1"
                    x-text="title"></h3>
                <p class="text-sm text-gray-600 dark:text-gray-300" x-html="message"></p>
                <p x-show="detail" class="text-xs text-gray-500 dark:text-gray-400 mt-2"
                    x-text="detail"></p>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 dark:border-gray-700">
            <button type="button" @click="cancel()"
                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg font-medium transition-colors"
                x-text="cancelText"></button>
            <button type="button" @click="confirm()" x-ref="confirmBtn"
                class="px-4 py-2 text-sm text-white rounded-lg font-medium transition-colors"
                :class="confirmBtnClass"
                x-text="confirmText"></button>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            // ─── Catalogos visuales por variante / icono ───────────────────────
            const UDEA_CONFIRM_VARIANTS = {
                danger:  { iconBg: 'bg-red-100 dark:bg-red-900/40',     iconColor: 'text-red-600 dark:text-red-400',     btn: 'bg-red-600 hover:bg-red-700' },
                warning: { iconBg: 'bg-amber-100 dark:bg-amber-900/40', iconColor: 'text-amber-600 dark:text-amber-400', btn: 'bg-amber-600 hover:bg-amber-700' },
                info:    { iconBg: 'bg-sky-100 dark:bg-sky-900/40',     iconColor: 'text-sky-600 dark:text-sky-400',     btn: 'bg-sky-600 hover:bg-sky-700' },
                success: { iconBg: 'bg-emerald-100 dark:bg-emerald-900/40', iconColor: 'text-emerald-600 dark:text-emerald-400', btn: 'bg-emerald-600 hover:bg-emerald-700' },
                primary: { iconBg: 'bg-[#0606F0]/10 dark:bg-[#0606F0]/25',  iconColor: 'text-[#0606F0] dark:text-blue-300',      btn: 'bg-[#0606F0] hover:bg-[#04276B]' },
            };

            const UDEA_CONFIRM_ICONS = {
                warning:     'M12 9v2m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z',
                trash:       'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3',
                info:        'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                check:       'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                question:    'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'arrow-right': 'M14 5l7 7m0 0l-7 7m7-7H3',
                'x-circle':  'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                ban:         'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728A9 9 0 015.636 5.636',
                clipboard:   'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                lock:        'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
            };

            // ─── Cola interna y promesa pendiente ─────────────────────────────
            window._udeaConfirmResolver = null;

            // ─── Store Alpine ─────────────────────────────────────────────────
            function udeaConfirmStore() {
                return {
                    open: false,
                    title: 'Confirmar',
                    message: '',
                    detail: '',
                    variant: 'primary',
                    icon: 'question',
                    confirmText: 'Aceptar',
                    cancelText: 'Cancelar',
                    iconBgClass: '',
                    iconColorClass: '',
                    confirmBtnClass: '',
                    iconPath: '',

                    init() {
                        // Exponer API global
                        const self = this;
                        window.udeaConfirm = (opts = {}) => new Promise((resolve) => {
                            self.show(opts, resolve);
                        });
                    },

                    show(opts, resolve) {
                        this.title       = opts.title       ?? 'Confirmar';
                        this.message     = opts.message     ?? '¿Continuar con esta accion?';
                        this.detail      = opts.detail      ?? '';
                        this.variant     = opts.variant     ?? 'primary';
                        this.icon        = opts.icon        ?? 'question';
                        this.confirmText = opts.confirmText ?? 'Aceptar';
                        this.cancelText  = opts.cancelText  ?? 'Cancelar';

                        const v = UDEA_CONFIRM_VARIANTS[this.variant] || UDEA_CONFIRM_VARIANTS.primary;
                        this.iconBgClass     = v.iconBg;
                        this.iconColorClass  = v.iconColor;
                        this.confirmBtnClass = v.btn;
                        this.iconPath        = UDEA_CONFIRM_ICONS[this.icon] || UDEA_CONFIRM_ICONS.question;

                        window._udeaConfirmResolver = resolve;
                        this.open = true;

                        // Focus al boton de confirmar (accesibilidad / Enter funciona)
                        this.$nextTick(() => { this.$refs.confirmBtn?.focus(); });
                    },

                    confirm() {
                        this.open = false;
                        const r = window._udeaConfirmResolver;
                        window._udeaConfirmResolver = null;
                        if (r) r(true);
                    },

                    cancel() {
                        if (!this.open) return;
                        this.open = false;
                        const r = window._udeaConfirmResolver;
                        window._udeaConfirmResolver = null;
                        if (r) r(false);
                    },
                };
            }

            // ─── Interceptor automatico para forms / botones declarativos ─────
            document.addEventListener('submit', function (e) {
                const form = e.target.closest('form[data-udea-confirm]');
                if (!form) return;
                if (form.dataset.udeaConfirmed === '1') {
                    // Ya pasamos por el modal y autorizamos: dejar pasar.
                    form.dataset.udeaConfirmed = '';
                    return;
                }
                e.preventDefault();
                window.udeaConfirm({
                    title:       form.dataset.confirmTitle,
                    message:     form.dataset.confirmMessage,
                    detail:      form.dataset.confirmDetail,
                    variant:     form.dataset.confirmVariant,
                    icon:        form.dataset.confirmIcon,
                    confirmText: form.dataset.confirmButton,
                    cancelText:  form.dataset.confirmCancel,
                }).then(ok => {
                    if (!ok) return;
                    form.dataset.udeaConfirmed = '1';
                    // HTMLFormElement.submit() NO dispara submit handlers, asi que reenviamos
                    // con requestSubmit() para preservar el boton que originó el envio cuando exista.
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                });
            }, true);

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('[data-udea-confirm]:not(form)');
                if (!btn) return;
                // Si el boton esta dentro de un form que TAMBIEN tiene data-udea-confirm,
                // dejamos que el handler de submit se encargue (evitamos doble modal).
                if (btn.tagName === 'BUTTON' && btn.type === 'submit' && btn.form?.matches('[data-udea-confirm]')) return;
                if (btn.dataset.udeaConfirmed === '1') {
                    btn.dataset.udeaConfirmed = '';
                    return;
                }
                e.preventDefault();
                e.stopPropagation();
                window.udeaConfirm({
                    title:       btn.dataset.confirmTitle,
                    message:     btn.dataset.confirmMessage,
                    detail:      btn.dataset.confirmDetail,
                    variant:     btn.dataset.confirmVariant,
                    icon:        btn.dataset.confirmIcon,
                    confirmText: btn.dataset.confirmButton,
                    cancelText:  btn.dataset.confirmCancel,
                }).then(ok => {
                    if (!ok) return;
                    btn.dataset.udeaConfirmed = '1';
                    btn.click();
                });
            }, true);
        </script>
    @endpush
@endonce
