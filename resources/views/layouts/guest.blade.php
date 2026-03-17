<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIGEA — Sistema de Gestion Educativa</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }

        /* ─── Left panel image overlay ─── */
        .left-panel {
            background: linear-gradient(145deg, #0a0a0a 0%, #1a1025 40%, #120f2a 70%, #0a0a0a 100%);
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 30% 20%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 80%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
            z-index: 1;
        }

        /* Floating particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
            animation: particleFloat 20s ease-in-out infinite;
        }
        .particle:nth-child(1) { width: 300px; height: 300px; top: -80px; left: -60px; animation-duration: 25s; }
        .particle:nth-child(2) { width: 200px; height: 200px; bottom: -40px; right: -40px; animation-delay: -8s; animation-duration: 22s; }
        .particle:nth-child(3) { width: 150px; height: 150px; top: 40%; left: 60%; animation-delay: -15s; }
        .particle:nth-child(4) { width: 80px; height: 80px; top: 20%; right: 20%; animation-delay: -5s; animation-duration: 18s; background: rgba(99, 102, 241, 0.08); }

        @keyframes particleFloat {
            0%, 100% { transform: translate(0, 0) scale(1) rotate(0deg); }
            25% { transform: translate(20px, -30px) scale(1.05) rotate(5deg); }
            50% { transform: translate(-15px, 15px) scale(0.95) rotate(-3deg); }
            75% { transform: translate(10px, 20px) scale(1.02) rotate(2deg); }
        }

        /* Grid pattern overlay */
        .grid-pattern {
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 50px 50px;
        }

        /* ─── Right panel animations ─── */
        .slide-up {
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }
        @keyframes slideUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .input-field {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
            border-color: #6366f1;
        }

        .btn-primary {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .btn-primary::after {
            content: '';
            position: absolute;
            top: -50%; left: -75%;
            width: 50%; height: 200%;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.1), transparent);
            transform: skewX(-25deg);
            transition: left 0.5s ease;
        }
        .btn-primary:hover::after { left: 125%; }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px -5px rgba(99, 102, 241, 0.4);
        }
        .btn-primary:active { transform: scale(0.98); }

        /* Logo pulse */
        .logo-glow {
            animation: logoGlow 3s ease-in-out infinite;
        }
        @keyframes logoGlow {
            0%, 100% { filter: drop-shadow(0 0 8px rgba(99, 102, 241, 0.3)); }
            50% { filter: drop-shadow(0 0 20px rgba(99, 102, 241, 0.6)); }
        }

        /* Stat counter */
        .stat-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Shake error */
        .shake { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }
        @keyframes shake {
            10%, 90% { transform: translateX(-1px); }
            20%, 80% { transform: translateX(2px); }
            30%, 50%, 70% { transform: translateX(-3px); }
            40%, 60% { transform: translateX(3px); }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 0; }
    </style>
</head>
<body class="antialiased bg-white min-h-screen">

    <div class="flex min-h-screen">

        {{-- ════════ LEFT PANEL — Imagen decorativa ════════ --}}
        <div class="hidden lg:flex lg:w-[52%] left-panel relative flex-col justify-between p-10 z-0">

            {{-- Particles --}}
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="absolute inset-0 grid-pattern z-[1]"></div>

            {{-- Top bar --}}
            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/10 backdrop-blur-xl rounded-xl flex items-center justify-center border border-white/10 logo-glow">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-white/90 text-[15px] font-bold tracking-tight">SIGEA</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="stat-card px-3 py-1.5 rounded-full text-[11px] text-white/60 font-medium">v2.0</span>
                </div>
            </div>

            {{-- Center content --}}
            <div class="relative z-10 max-w-md">
                <div class="mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-400/20 text-indigo-300 text-[11px] font-semibold uppercase tracking-wider mb-5">
                        <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full animate-pulse"></span>
                        Plataforma Educativa
                    </span>
                </div>
                <h2 class="text-[42px] font-extrabold text-white leading-[1.1] tracking-tight mb-4">
                    Gestion<br>
                    <span class="bg-gradient-to-r from-indigo-400 via-violet-400 to-purple-400 bg-clip-text text-transparent">Academica</span><br>
                    Integral
                </h2>
                <p class="text-[15px] text-white/40 leading-relaxed max-w-sm">
                    Administra calificaciones, horarios, evaluaciones y mas desde un solo lugar.
                </p>
            </div>

            {{-- Bottom stats --}}
            <div class="relative z-10 flex items-center gap-4">
                <div class="stat-card rounded-2xl px-5 py-4 flex-1">
                    <p class="text-[24px] font-extrabold text-white">500+</p>
                    <p class="text-[11px] text-white/40 font-medium mt-0.5">Alumnos activos</p>
                </div>
                <div class="stat-card rounded-2xl px-5 py-4 flex-1">
                    <p class="text-[24px] font-extrabold text-white">50+</p>
                    <p class="text-[11px] text-white/40 font-medium mt-0.5">Docentes</p>
                </div>
                <div class="stat-card rounded-2xl px-5 py-4 flex-1">
                    <p class="text-[24px] font-extrabold text-white">15+</p>
                    <p class="text-[11px] text-white/40 font-medium mt-0.5">Carreras</p>
                </div>
            </div>
        </div>

        {{-- ════════ RIGHT PANEL — Formulario ════════ --}}
        <div class="flex-1 flex items-center justify-center px-6 py-10 bg-[#fafafa] relative">

            {{-- Subtle background pattern --}}
            <div class="absolute inset-0 opacity-[0.3]" style="background-image: radial-gradient(#e5e7eb 1px, transparent 1px); background-size: 24px 24px;"></div>

            <div class="w-full max-w-[400px] relative z-10">

                {{-- Brand --}}
                <div class="text-center mb-8 slide-up">
                    {{-- Avatar / Logo circle --}}
                    <div class="inline-flex items-center justify-center w-[72px] h-[72px] bg-gradient-to-br from-indigo-600 to-violet-600 rounded-full mb-5 shadow-lg shadow-indigo-200">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h1 class="text-[13px] font-bold text-gray-400 uppercase tracking-[0.15em] mb-1">SIGEA</h1>
                </div>

                {{-- Form card --}}
                <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 px-8 py-9 slide-up" style="animation-delay: 0.1s;">
                    {{ $slot }}
                </div>

                {{-- Footer --}}
                <p class="text-center text-gray-300 text-[11px] mt-7 slide-up font-medium" style="animation-delay: 0.3s;">
                    &copy; {{ date('Y') }} SIGEA &mdash; Todos los derechos reservados
                </p>
            </div>
        </div>
    </div>

</body>
</html>
