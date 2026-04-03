<div x-data="{
        theme: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
        apply() {
            if (this.theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        toggle() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', this.theme);
            this.apply();
        }
     }"
     x-init="apply()"
>
    <button @click="toggle()" :aria-label="theme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'" :title="theme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
            class="p-2.5 rounded-xl text-[#121D30]/40 dark:text-[#F7F7F7]/40 hover:text-[#0606F0] dark:hover:text-[#F7F7F7] hover:bg-[#04276B]/5 dark:hover:bg-white/10 transition-colors icon-hover">
        {{-- Sol (mostrar cuando estÃ¡ en dark, para cambiar a light) --}}
        <svg x-show="theme === 'dark'" x-cloak class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        {{-- Luna (mostrar cuando estÃ¡ en light, para cambiar a dark) --}}
        <svg x-show="theme === 'light'" x-cloak class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
        </svg>
    </button>
</div>
