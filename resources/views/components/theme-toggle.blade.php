<div x-data="{
        theme: localStorage.getItem('theme') || 'system',
        apply() {
            if (this.theme === 'dark' || (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        cycle() {
            const order = ['light', 'dark', 'system'];
            this.theme = order[(order.indexOf(this.theme) + 1) % 3];
            if (this.theme === 'system') {
                localStorage.removeItem('theme');
            } else {
                localStorage.setItem('theme', this.theme);
            }
            this.apply();
        },
        label() {
            return { light: 'Cambiar a modo oscuro', dark: 'Cambiar a modo sistema', system: 'Cambiar a modo claro' }[this.theme];
        }
     }"
     x-init="apply(); window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => { if (theme === 'system') apply(); })"
>
    <button @click="cycle()" :aria-label="label()" :title="label()"
            class="p-2.5 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-white/60 dark:hover:text-gray-200 dark:hover:bg-white/10 transition-colors">
        {{-- Sol (light) --}}
        <svg x-show="theme === 'light'" x-cloak class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        {{-- Luna (dark) --}}
        <svg x-show="theme === 'dark'" x-cloak class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
        </svg>
        {{-- Monitor (system) --}}
        <svg x-show="theme === 'system'" x-cloak class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </button>
</div>
