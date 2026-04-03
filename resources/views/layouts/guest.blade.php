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
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', system-ui, sans-serif; overflow: hidden; }

        /* ═══════════════════════════════════════════
           FULL-SCREEN ANIMATED BACKGROUND
           ═══════════════════════════════════════════ */
        .bg-animated {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #0f2b7a 0%, #1a4fc7 25%, #2563eb 50%, #1e3faf 75%, #0f2060 100%);
            overflow: hidden;
        }

        /* Fluid blobs — faster, more varied colors */
        .fluid-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
            mix-blend-mode: screen;
            will-change: transform;
        }
        .blob-1 {
            width: 420px; height: 420px;
            background: radial-gradient(circle, rgba(56, 182, 255, 0.65) 0%, rgba(30, 100, 220, 0.2) 50%, transparent 70%);
            top: -10%; left: -10%;
            animation: b1 5s ease-in-out infinite;
        }
        .blob-2 {
            width: 380px; height: 380px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.45) 0%, rgba(100, 180, 255, 0.15) 50%, transparent 70%);
            top: 15%; right: 5%;
            animation: b2 6s ease-in-out infinite;
        }
        .blob-3 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(100, 80, 255, 0.5) 0%, rgba(80, 40, 200, 0.15) 50%, transparent 70%);
            bottom: -15%; left: 20%;
            animation: b3 7s ease-in-out infinite;
        }
        .blob-4 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(130, 220, 255, 0.55) 0%, rgba(60, 160, 240, 0.1) 50%, transparent 70%);
            top: 45%; left: 5%;
            animation: b4 4.5s ease-in-out infinite;
        }
        .blob-5 {
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(160, 100, 255, 0.45) 0%, rgba(120, 60, 220, 0.1) 50%, transparent 70%);
            top: 5%; left: 40%;
            animation: b5 5.5s ease-in-out infinite;
        }
        .blob-6 {
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(0, 200, 255, 0.4) 0%, rgba(0, 120, 200, 0.1) 50%, transparent 70%);
            bottom: 10%; right: 15%;
            animation: b6 6.5s ease-in-out infinite;
        }
        .blob-7 {
            width: 260px; height: 260px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 60%);
            top: 60%; left: 45%;
            animation: b7 4s ease-in-out infinite;
        }

        @keyframes b1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(60px, 50px) scale(1.15); }
            50% { transform: translate(30px, 90px) scale(0.9); }
            75% { transform: translate(-30px, 30px) scale(1.08); }
        }
        @keyframes b2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            20% { transform: translate(-50px, 60px) scale(1.12); }
            50% { transform: translate(-80px, -30px) scale(0.88); }
            80% { transform: translate(20px, -50px) scale(1.06); }
        }
        @keyframes b3 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            30% { transform: translate(70px, -50px) scale(1.18); }
            60% { transform: translate(-40px, -80px) scale(0.85); }
        }
        @keyframes b4 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            35% { transform: translate(-60px, -40px) scale(1.2); }
            65% { transform: translate(50px, 30px) scale(0.85); }
        }
        @keyframes b5 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            30% { transform: translate(-40px, 60px) scale(1.15); }
            70% { transform: translate(35px, -45px) scale(0.9); }
        }
        @keyframes b6 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            40% { transform: translate(-50px, -40px) scale(1.1); }
            75% { transform: translate(30px, 50px) scale(0.92); }
        }
        @keyframes b7 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(40px, -35px) scale(1.18); }
        }

        /* ═══════════════════════════════════════════
           LAYOUT — Two columns over animated bg
           ═══════════════════════════════════════════ */
        .page-layout {
            position: relative;
            z-index: 1;
            display: flex;
            min-height: 100vh;
        }

        /* ─── Left: branding ─── */
        .left-panel {
            width: 44%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px 36px;
            position: relative;
            z-index: 2;
        }

        /* ─── Right: glass form ─── */
        .right-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 52px 48px;
            position: relative;
            z-index: 2;
            background: rgb(255, 255, 255);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border-left: 1px solid rgba(255, 255, 255, 0.4);
        }

        /* ─── Inputs ─── */
        .login-input {
            width: 100%;
            background: rgb(255, 255, 255);
            border: 1.5px solid rgba(200, 210, 230, 0.6);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            color: #111827;
            outline: none;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
            backdrop-filter: blur(8px);
        }
        .login-input::placeholder { color: #9CA3AF; }
        .login-input:focus {
            border-color: #3B6CF6;
            box-shadow: 0 0 0 3px rgba(59,108,246,0.12);
            background: rgba(255, 255, 255, 0.85);
        }

        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        /* ─── Submit button ─── */
        .login-btn {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #3B6CF6 0%, #2952E3 100%);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(59,108,246,0.35);
        }
        .login-btn:active { transform: scale(0.98); }

        .pass-toggle {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            display: flex;
            align-items: center;
            transition: color 0.15s;
        }
        .pass-toggle:hover { color: #3B6CF6; }

        /* ─── Animations ─── */
        .fade-up {
            animation: fadeUp 0.55s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(14px);
        }
        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }
        .shake { animation: shake 0.45s cubic-bezier(.36,.07,.19,.97) both; }
        @keyframes shake {
            10%, 90% { transform: translateX(-1px); }
            20%, 80% { transform: translateX(2px); }
            30%, 50%, 70% { transform: translateX(-3px); }
            40%, 60% { transform: translateX(3px); }
        }

        /* ─── Responsive ─── */
        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { padding: 40px 28px; }
        }
        @media (max-width: 440px) {
            .right-panel { padding: 32px 20px; }
        }
    </style>
</head>
<body class="antialiased">

    {{-- Full-screen animated gradient background --}}
    <div class="bg-animated">
        <div class="fluid-blob blob-1"></div>
        <div class="fluid-blob blob-2"></div>
        <div class="fluid-blob blob-3"></div>
        <div class="fluid-blob blob-4"></div>
        <div class="fluid-blob blob-5"></div>
        <div class="fluid-blob blob-6"></div>
        <div class="fluid-blob blob-7"></div>
    </div>

    {{-- Content layout --}}
    <div class="page-layout">

        {{-- ═══════ LEFT — Branding ═══════ --}}
        <div class="left-panel">

            {{-- Logo --}}
            <div class="fade-up">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:38px; height:38px; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); border-radius:10px; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(8px);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span style="color:rgba(255,255,255,0.9); font-size:15px; font-weight:700; letter-spacing:0.02em;">SIGEA</span>
                </div>
            </div>

            {{-- Headline --}}
            <div class="fade-up" style="animation-delay:0.12s;">
                <p style="color:rgba(255,255,255,0.55); font-size:13px; font-weight:500; margin-bottom:14px;">
                    Plataforma educativa
                </p>
                <h2 style="color:#ffffff; font-size:70px; font-weight:800; line-height:1.12; letter-spacing:-0.025em;">
                    Sistema de<br>Gestion<br>
                    <span style="background: linear-gradient(135deg, rgba(255,255,255,1), rgba(180,210,255,0.8)); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">Academica</span>
                </h2>
            </div>

            {{-- Footer --}}
            <div class="fade-up" style="animation-delay:0.22s;">
                <p style="color:rgba(255,255,255,0.3); font-size:11px;">
                    &copy; {{ date('Y') }} SIGEA — Todos los derechos reservados
                </p>
            </div>

        </div>

        {{-- ═══════ RIGHT — Glass form ═══════ --}}
        <div class="right-panel">
            {{ $slot }}
        </div>

    </div>

</body>
</html>
