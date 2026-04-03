<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIGEA' }} — SIGEA</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <script>
        (function(){
            var t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
        }

        /* ─── Rainbow Progress Bar ─── */
        .rainbow-bar {
            height: 100%;
            border-radius: 9999px;
            background: linear-gradient(90deg,
                    #0606F0, #04276B, #1C1E46, #0606F0,
                    #E5CCBE, #0606F0, #04276B, #1C1E46,
                    #0606F0, #E5CCBE, #0606F0);
            background-size: 300% 100%;
            animation: rainbowShift 60s linear infinite;
            position: relative;
            overflow: hidden;
        }

        .rainbow-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: -50%;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.35),
                    transparent);
            animation: rainbowShine 240s ease-in-out infinite;
        }

        @keyframes rainbowShift {
            0% { background-position: 0% 50%; }
            100% { background-position: 300% 50%; }
        }

        @keyframes rainbowShine {
            0% { left: -50%; }
            100% { left: 150%; }
        }

        /* Track (container) for rainbow bars */
        .rainbow-track {
            background: #f3f4f6;
            border-radius: 9999px;
            overflow: hidden;
            position: relative;
        }
        .dark .rainbow-track {
            background: rgba(247, 247, 247, 0.1);
        }

        /* Glow under the bar */
        .rainbow-glow {
            filter: drop-shadow(0 0 6px rgba(6, 6, 240, 0.3)) drop-shadow(0 0 12px rgba(4, 39, 107, 0.15));
        }

        /* ═══════════════════════════════════════════
           GSAP-like CSS Animations
           ═══════════════════════════════════════════ */

        /* ─── Staggered fade-in for content ─── */
        .fade-in {
            animation: gsapFadeIn 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            opacity: 0;
        }
        @keyframes gsapFadeIn {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ─── Sidebar nav items: staggered slide-in from left ─── */
        .sidebar-link {
            animation: sidebarSlideIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            opacity: 0;
            transform: translateX(-16px);
        }
        @for ($i = 1; $i <= 20; $i++)
        .sidebar-link:nth-child({{ $i }}) { animation-delay: {{ 0.04 * $i }}s; }
        @endfor
        @keyframes sidebarSlideIn {
            to { opacity: 1; transform: translateX(0); }
        }

        /* ─── Cards: scale + fade entrance ─── */
        .card-enter {
            animation: cardEnter 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            opacity: 0;
            transform: translateY(24px) scale(0.97);
        }
        @keyframes cardEnter {
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ─── Card hover: spring lift ─── */
        .card-hover {
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1),
                        box-shadow 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .card-hover:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 12px 40px -8px rgba(4, 39, 107, 0.15),
                        0 4px 12px -2px rgba(0, 0, 0, 0.06);
        }
        .dark .card-hover:hover {
            box-shadow: 0 12px 40px -8px rgba(0, 0, 0, 0.4),
                        0 4px 12px -2px rgba(0, 0, 0, 0.2);
        }

        /* ─── Stat numbers: count-up reveal ─── */
        .stat-reveal {
            animation: statReveal 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            opacity: 0;
            transform: translateY(12px) scale(0.9);
        }
        @keyframes statReveal {
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ─── Header breadcrumb: slide from right ─── */
        .breadcrumb-enter {
            animation: breadcrumbIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            opacity: 0;
            transform: translateX(-10px);
        }
        @keyframes breadcrumbIn {
            to { opacity: 1; transform: translateX(0); }
        }

        /* ─── Logo: subtle pulse on load ─── */
        .logo-enter {
            animation: logoEnter 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            opacity: 0;
            transform: scale(0.8);
        }
        @keyframes logoEnter {
            to { opacity: 1; transform: scale(1); }
        }

        /* ─── Nav group labels: fade in ─── */
        .nav-label-enter {
            animation: navLabelIn 0.4s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            opacity: 0;
        }
        @keyframes navLabelIn {
            to { opacity: 1; }
        }

        /* ─── Button interactions: micro spring ─── */
        .btn-spring {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
                        box-shadow 0.3s ease;
        }
        .btn-spring:hover {
            transform: translateY(-2px);
        }
        .btn-spring:active {
            transform: translateY(0) scale(0.96);
        }

        /* ─── Icon buttons: smooth rotate on hover ─── */
        .icon-hover {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
                        color 0.2s ease;
        }
        .icon-hover:hover {
            transform: scale(1.12) rotate(-6deg);
        }

        /* ─── User section: slide up on load ─── */
        .user-enter {
            animation: userEnter 0.5s cubic-bezier(0.22, 1, 0.36, 1) 0.6s forwards;
            opacity: 0;
            transform: translateY(10px);
        }
        @keyframes userEnter {
            to { opacity: 1; transform: translateY(0); }
        }

        /* ─── Notification dot pulse ─── */
        .pulse-dot {
            animation: pulseDot 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.5); }
        }

        /* ─── Notification badge ─── */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ─── Page transition wrapper ─── */
        .page-transition {
            animation: pageSlide 0.65s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            opacity: 0;
        }
        @keyframes pageSlide {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ─── Smooth scrollbar ─── */
        .custom-scrollbar {
            scroll-behavior: smooth;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(4, 39, 107, 0.15);
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(4, 39, 107, 0.3);
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(247, 247, 247, 0.15);
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(247, 247, 247, 0.3);
        }
    </style>
</head>

<body class="antialiased bg-[#EEEEEE] dark:bg-[#121D30]">

    <div class="flex h-screen overflow-hidden">

        {{-- ======== SIDEBAR ======== --}}
        <aside class="w-[220px] bg-white dark:bg-[#1C1E46] border-r border-gray-200 dark:border-white/10 flex flex-col flex-shrink-0">

            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 h-16 border-b border-gray-100 dark:border-white/10">
                <div class="w-9 h-9 bg-[#04276B] dark:bg-[#0606F0] rounded-xl flex items-center justify-center logo-enter">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="text-[15px] font-bold text-[#121D30] dark:text-[#F7F7F7] tracking-tight">SIGEA</span>
            </div>

            {{-- Menú label --}}
            <div class="px-5 pt-5 pb-2">
                <p class="text-[10px] font-semibold text-[#04276B]/50 dark:text-[#E5CCBE] uppercase tracking-[0.12em]">
                    {{ $panelNombre ?? 'Menú' }}
                </p>
            </div>

            {{-- Navegación --}}
            <nav class="flex-1 overflow-y-auto custom-scrollbar px-3 space-y-0.5 pb-4">
                {{ $nav ?? '' }}
            </nav>

            {{-- Separador --}}
            <div class="border-t border-gray-100 dark:border-white/10"></div>

            {{-- Cambiar contraseña --}}
            <div class="px-3 pt-3">
                <a href="{{ route('cambiar-password') }}"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[12px] font-medium text-[#121D30]/40 dark:text-white/50 hover:text-[#0606F0] dark:hover:text-white hover:bg-[#04276B]/5 dark:hover:bg-white/10 transition-colors {{ request()->routeIs('cambiar-password') ? 'bg-[#04276B]/5 dark:bg-white/10 text-[#0606F0] dark:text-white' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Cambiar contraseña
                </a>
            </div>

            {{-- Usuario --}}
            <div class="p-4 flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-full bg-gradient-to-br from-[#04276B] to-[#0606F0] flex items-center justify-center text-white text-xs font-bold shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-semibold text-[#121D30] dark:text-[#F7F7F7] truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-[#04276B]/40 dark:text-[#E5CCBE]/70 truncate">
                        @php
                            $user = auth()->user();
                            $rolLabel = match (true) {
                                $user->hasRole('servicios_escolares') => 'Servicios Escolares',
                                $user->hasRole('director_carrera') => 'Director de Carrera',
                                $user->hasRole('docente') => 'Docente',
                                $user->hasRole('alumno') => 'Alumno',
                                default => 'Usuario',
                            };
                        @endphp
                        {{ $rolLabel }}
                    </p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Cerrar sesion"
                        class="p-1.5 rounded-lg text-[#121D30]/30 dark:text-white/40 hover:text-red-500 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-500/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        {{-- ======== CONTENIDO PRINCIPAL ======== --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Header --}}
            <header class="bg-[#EEEEEE] dark:bg-[#121D30] flex items-center justify-between px-7 h-14 flex-shrink-0">
                {{-- Breadcrumb --}}
                <div class="flex items-center gap-2 text-[13px] breadcrumb-enter">
                    <span class="text-[#121D30]/40 dark:text-[#F7F7F7]/40">SIGEA</span>
                    <svg class="w-3.5 h-3.5 text-[#121D30]/25 dark:text-[#F7F7F7]/25" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-[#121D30]/40 dark:text-[#F7F7F7]/40">{{ $panelNombre ?? 'Panel' }}</span>
                    <svg class="w-3.5 h-3.5 text-[#121D30]/25 dark:text-[#F7F7F7]/25" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="font-medium text-[#121D30] dark:text-[#F7F7F7]">{{ $title ?? 'Dashboard' }}</span>
                </div>

                {{-- Acciones header --}}
                <div class="flex items-center gap-1">
                    {{-- Theme toggle --}}
                    <x-theme-toggle />
                    {{-- Notificaciones --}}
                    <div x-data="notificaciones()" x-init="init()" class="relative">
                        <button @click="toggle()" class="relative p-2.5 rounded-xl text-[#121D30]/40 dark:text-[#F7F7F7]/40 hover:text-[#0606F0] dark:hover:text-[#F7F7F7] hover:bg-[#04276B]/5 dark:hover:bg-white/10 transition-colors">
                            <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span x-show="noLeidas > 0" x-transition
                                  class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] flex items-center justify-center bg-[#0606F0] text-white text-[10px] font-bold rounded-full px-1 shadow-sm shadow-[#0606F0]/30"
                                  x-text="noLeidas > 99 ? '99+' : noLeidas"></span>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="abierto" @click.outside="abierto = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                             class="absolute right-0 top-full mt-2 w-[380px] bg-white dark:bg-gray-800 rounded-2xl shadow-xl dark:shadow-gray-900/50 border border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
                             style="display: none;">

                            {{-- Header --}}
                            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <h3 class="text-[14px] font-bold text-gray-800 dark:text-gray-100">Notificaciones</h3>
                                <button x-show="noLeidas > 0" @click="marcarTodasLeidas()"
                                        class="text-[11px] font-medium text-[#0606F0] dark:text-blue-400 hover:underline">
                                    Marcar todas como leídas
                                </button>
                            </div>

                            {{-- Lista --}}
                            <div class="max-h-[400px] overflow-y-auto custom-scrollbar">
                                <template x-if="items.length === 0">
                                    <div class="px-5 py-10 text-center">
                                        <svg class="w-10 h-10 text-gray-200 dark:text-gray-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        <p class="text-[13px] text-gray-400 dark:text-gray-500">Sin notificaciones</p>
                                    </div>
                                </template>

                                <template x-for="item in items" :key="item.id">
                                    <div @click="abrirNotificacion(item)"
                                         :class="item.leida ? 'opacity-60' : 'bg-[#0606F0]/[0.03] dark:bg-blue-900/10'"
                                         class="px-5 py-3.5 flex gap-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-50 dark:border-gray-700/50 last:border-b-0">
                                        {{-- Icono --}}
                                        <div class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center" :class="item.color_class">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" :d="item.icono_svg"/>
                                            </svg>
                                        </div>
                                        {{-- Contenido --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <p class="text-[12px] font-semibold text-gray-800 dark:text-gray-200 leading-tight" x-text="item.titulo"></p>
                                                <span x-show="!item.leida" class="flex-shrink-0 w-2 h-2 bg-[#0606F0] rounded-full mt-1"></span>
                                            </div>
                                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2" x-text="item.mensaje"></p>
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1" x-text="item.tiempo"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    {{-- Chat --}}
                    <button id="btn-chat-toggle" onclick="toggleChatbot()"
                        class="p-2.5 rounded-xl text-[#121D30]/40 dark:text-[#F7F7F7]/40 hover:text-[#0606F0] dark:hover:text-[#F7F7F7] hover:bg-[#04276B]/5 dark:hover:bg-white/10 transition-colors relative">
                        <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </button>
                    {{-- Buscar --}}
                    <button class="p-2.5 rounded-xl text-[#121D30]/40 dark:text-[#F7F7F7]/40 hover:text-[#0606F0] dark:hover:text-[#F7F7F7] hover:bg-[#04276B]/5 dark:hover:bg-white/10 transition-colors">
                        <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </header>

            {{-- Alertas de sesión --}}
            @if (session('success'))
                <div
                    class="mx-7 mb-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-2xl text-[13px] flex items-center gap-2 fade-in">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div
                    class="mx-7 mb-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-2xl text-[13px] flex items-center gap-2 fade-in">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Contenido --}}
            <main class="flex-1 overflow-y-auto custom-scrollbar px-7 pb-7 pt-1">
                <div class="fade-in">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')

    {{-- ════════ CHATBOT FLOTANTE (TODOS LOS ROLES) ════════ --}}
    @auth
        @php
            $chatUser = auth()->user();
            $chatRol = match (true) {
                $chatUser->hasRole('servicios_escolares') => 'servicios',
                $chatUser->hasRole('director_carrera') => 'director',
                $chatUser->hasRole('docente') => 'docente',
                $chatUser->hasRole('alumno') => 'alumno',
                default => null,
            };

            $chatRoute = match ($chatRol) {
                'alumno' => route('alumno.chatbot'),
                'docente' => route('docente.chatbot'),
                'director' => route('director.chatbot'),
                'servicios' => route('servicios.chatbot'),
                default => null,
            };

            $chatSugerencias = match ($chatRol) {
                'alumno' => ['Calificaciones', 'Horas ACUDE', 'Horario', 'Servicio Social', 'Kardex', 'Docentes'],
                'docente' => ['Mis Grupos', 'Horario', 'Asistencia', 'Calificaciones', 'Evaluaciones', 'Reportes'],
                'director' => ['Alumnos', 'Docentes', 'Grupos', 'Aprobacion', 'Evaluaciones', 'Plan de Estudios'],
                'servicios' => ['Alumnos', 'Docentes', 'Inscripciones', 'Constancias', 'Carreras', 'Reportes'],
                default => [],
            };

            $chatDescripcion = match ($chatRol) {
                'alumno' => 'Pregunta sobre calificaciones, horario, horas ACUDE y mas.',
                'docente' => 'Pregunta sobre tus grupos, asistencia, calificaciones y mas.',
                'director' => 'Consulta estadisticas de carrera, docentes, alumnos y mas.',
                'servicios' => 'Consulta sobre alumnos, inscripciones, reportes y mas.',
                default => 'Pregunta lo que necesites.',
            };
        @endphp

        @if($chatRoute)
            <div id="chatbot-panel"
                class="fixed z-50 right-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden flex-col" style="top: 60px; width: 300px; display: none;
                                max-height: calc(100vh - 80px);
                                box-shadow: 0 20px 60px -10px rgba(0,0,0,0.18), 0 4px 20px -4px rgba(0,0,0,0.08);">

                {{-- Header --}}
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="text-[13px] font-bold text-gray-800 dark:text-gray-100">Asistente SIGEA</span>
                        <span class="text-[10px] font-semibold bg-[#0606F0] text-white px-2.5 py-0.5 rounded-full">IA</span>
                    </div>
                    <button onclick="toggleChatbot()"
                        class="p-1 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Mensajes --}}
                <div id="chat-messages" class="overflow-y-auto p-5 space-y-3 flex-1"
                    style="min-height: 280px; max-height: 380px;">
                    {{-- Bienvenida --}}
                    <div class="text-center py-4" id="chat-welcome">
                        <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br from-[#0606F0] to-[#04276B]
                                            flex items-center justify-center mb-4 shadow-lg shadow-[#0606F0]/20">
                            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h3 class="text-[16px] font-bold text-gray-800 dark:text-gray-100 mb-1">¿En que te ayudo?</h3>
                        <p class="text-[11px] text-gray-400 leading-relaxed px-2">
                            {{ $chatDescripcion }}
                        </p>
                    </div>

                    {{-- Sugerencias rápidas según rol --}}
                    <div class="flex flex-wrap gap-1.5 justify-center" id="sugerencias">
                        @foreach($chatSugerencias as $sug)
                            <button onclick="enviarSugerencia(this)" class="text-[11px] font-medium bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300
                                                       px-3 py-1.5 rounded-full transition-colors">
                                {{ $sug }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Input --}}
                <div class="p-4 border-t border-gray-100 dark:border-gray-700 flex-shrink-0">
                    <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 rounded-xl px-3.5 py-2.5">
                        <input type="text" id="chat-input" placeholder="Escribe tu pregunta..."
                            class="flex-1 bg-transparent text-[13px] text-gray-700 dark:text-gray-200 outline-none placeholder-gray-400"
                            onkeydown="if(event.key==='Enter') enviarMensaje()" />
                        <button onclick="enviarMensaje()" class="w-8 h-8 bg-[#0606F0] rounded-lg flex items-center justify-center
                                           hover:bg-[#04276B] flex-shrink-0 transition-colors">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <style>
                .typing-dots span {
                    display: inline-block;
                    width: 6px;
                    height: 6px;
                    background: #9ca3af;
                    border-radius: 50%;
                    margin: 0 1px;
                    animation: typingBounce 1.2s infinite;
                }

                .typing-dots span:nth-child(2) {
                    animation-delay: 0.2s;
                }

                .typing-dots span:nth-child(3) {
                    animation-delay: 0.4s;
                }

                @keyframes typingBounce {

                    0%,
                    60%,
                    100% {
                        transform: translateY(0);
                    }

                    30% {
                        transform: translateY(-6px);
                    }
                }

                #chatbot-panel {
                    transition: opacity 0.18s ease, transform 0.18s ease;
                }

                #chatbot-panel.chat-open {
                    opacity: 1;
                    transform: translateY(0);
                }

                #chatbot-panel.chat-closed {
                    opacity: 0;
                    transform: translateY(-6px);
                }
            </style>

            <script>
                const CSRF = document.querySelector('meta[name="csrf-token"]').content;
                const CHAT_URL = '{{ $chatRoute }}';
                let chatOpen = false;
                const isDark = () => document.documentElement.classList.contains('dark');

                function toggleChatbot() {
                    const panel = document.getElementById('chatbot-panel');
                    const btn = document.getElementById('btn-chat-toggle');
                    if (!chatOpen) {
                        panel.style.display = 'flex';
                        panel.style.flexDirection = 'column';
                        setTimeout(() => panel.classList.add('chat-open'), 10);
                        panel.classList.remove('chat-closed');
                    } else {
                        panel.classList.remove('chat-open');
                        panel.classList.add('chat-closed');
                        setTimeout(() => { panel.style.display = 'none'; }, 180);
                    }
                    chatOpen = !chatOpen;
                }

                function agregarMensaje(texto, esUsuario) {
                    const c = document.getElementById('chat-messages');
                    const d = document.createElement('div');
                    d.className = 'flex ' + (esUsuario ? 'justify-end' : 'justify-start');
                    const dark = isDark();
                    d.innerHTML = `<div class="max-w-[85%] px-3.5 py-2.5 rounded-2xl text-[13px] leading-relaxed ${esUsuario
                        ? 'bg-[#0606F0] text-white rounded-br-md'
                        : (dark ? 'bg-[#1C1E46] text-[#F7F7F7] rounded-bl-md' : 'bg-[#EEEEEE] text-[#121D30] rounded-bl-md')
                        }">${texto}</div>`;
                    c.appendChild(d);
                    c.scrollTop = c.scrollHeight;
                }

                function enviarSugerencia(btn) {
                    document.getElementById('chat-welcome')?.remove();
                    document.getElementById('sugerencias')?.remove();
                    procesarMensaje(btn.textContent.trim());
                }

                function enviarMensaje() {
                    const input = document.getElementById('chat-input');
                    const texto = input.value.trim();
                    if (!texto) return;
                    input.value = '';
                    document.getElementById('chat-welcome')?.remove();
                    document.getElementById('sugerencias')?.remove();
                    procesarMensaje(texto);
                }

                function procesarMensaje(texto) {
                    agregarMensaje(texto, true);
                    const c = document.getElementById('chat-messages');
                    const t = document.createElement('div');
                    t.id = 'typing';
                    t.className = 'flex justify-start';
                    const dark = isDark();
                    t.innerHTML = `<div class="${dark ? 'bg-[#1C1E46] text-[#F7F7F7]/50' : 'bg-[#EEEEEE] text-[#121D30]/50'} px-3.5 py-2.5 rounded-2xl rounded-bl-md"><span class="typing-dots"><span></span><span></span><span></span></span></div>`;
                    c.appendChild(t);
                    c.scrollTop = c.scrollHeight;

                    fetch(CHAT_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body: JSON.stringify({ mensaje: texto })
                    })
                        .then(r => r.json())
                        .then(data => {
                            document.getElementById('typing')?.remove();
                            agregarMensaje(data.respuesta ?? 'Sin respuesta.', false);
                        })
                        .catch(() => {
                            document.getElementById('typing')?.remove();
                            agregarMensaje('Error al contactar al asistente.', false);
                        });
                }
            </script>
        @endif
    @endauth

    {{-- Script de Notificaciones --}}
    @auth
    <script>
        function notificaciones() {
            return {
                abierto: false,
                items: [],
                noLeidas: 0,
                polling: null,

                init() {
                    this.cargar();
                    // Polling cada 30 segundos
                    this.polling = setInterval(() => this.cargar(), 30000);
                },

                async cargar() {
                    try {
                        const res = await fetch('/notificaciones', {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (!res.ok) return;
                        const data = await res.json();
                        const prevNoLeidas = this.noLeidas;
                        this.items = data.notificaciones;
                        this.noLeidas = data.no_leidas;

                        // Efecto visual si hay nuevas
                        if (data.no_leidas > prevNoLeidas && prevNoLeidas > 0) {
                            this.animarBadge();
                        }
                    } catch (e) { /* silenciar errores de red */ }
                },

                toggle() {
                    this.abierto = !this.abierto;
                    if (this.abierto) this.cargar();
                },

                async abrirNotificacion(item) {
                    if (!item.leida) {
                        await fetch(`/notificaciones/${item.id}/leida`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        item.leida = true;
                        this.noLeidas = Math.max(0, this.noLeidas - 1);
                    }
                    if (item.url) {
                        this.abierto = false;
                        window.location.href = item.url;
                    }
                },

                async marcarTodasLeidas() {
                    await fetch('/notificaciones/marcar-todas', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    this.items.forEach(i => i.leida = true);
                    this.noLeidas = 0;
                },

                animarBadge() {
                    // Vibración visual en el badge
                    const badge = this.$el?.querySelector('[x-text]');
                    if (badge) {
                        badge.classList.add('ring-2', 'ring-[#0606F0]/40');
                        setTimeout(() => badge.classList.remove('ring-2', 'ring-[#0606F0]/40'), 2000);
                    }
                }
            };
        }
    </script>
    @endauth

</body>

</html>
