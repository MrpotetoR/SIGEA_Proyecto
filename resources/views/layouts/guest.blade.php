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
        body { font-family: 'Inter', system-ui, sans-serif; }

        .page-wrap {
            min-height: 100vh;
            display: flex;
        }

        /* ─── Split card ─── */
        .split-card {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ═══════════════════════════════════════════
           LEFT PANEL — Fluid animated gradient
           ═══════════════════════════════════════════ */
        .left-panel {
            width: 44%;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px 36px;
            overflow: hidden;
            background: linear-gradient(135deg, #1a3a8a 0%, #1e56c7 30%, #2b6dd6 50%, #1a3a8a 100%);
        }

        /* Fluid blobs that animate to create liquid gradient effect */
        .fluid-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(50px);
            pointer-events: none;
            mix-blend-mode: screen;
            will-change: transform;
        }
        .blob-1 {
            width: 320px; height: 320px;
            background: radial-gradient(circle, rgba(56, 152, 255, 0.7) 0%, rgba(30, 90, 200, 0.3) 50%, transparent 70%);
            top: -15%; left: -20%;
            animation: blobMove1 8s ease-in-out infinite;
        }
        .blob-2 {
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.6) 0%, rgba(60, 120, 220, 0.25) 50%, transparent 70%);
            top: 25%; right: -25%;
            animation: blobMove2 10s ease-in-out infinite;
        }
        .blob-3 {
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(80, 100, 240, 0.55) 0%, rgba(100, 60, 200, 0.2) 50%, transparent 70%);
            bottom: -20%; left: 10%;
            animation: blobMove3 12s ease-in-out infinite;
        }
        .blob-4 {
            width: 200px; height: 200px;
            background: radial-gradient(circle, hsla(204, 100%, 99%, 0.50) 0%, rgba(199, 227, 255, 0.15) 50%, transparent 70%);
            top: 50%; left: 30%;
            animation: blobMove4 9s ease-in-out infinite;
        }
        .blob-5 {
            width: 240px; height: 240px;
            background: radial-gradient(circle, rgba(140, 100, 255, 0.4) 0%, rgba(100, 60, 200, 0.15) 50%, transparent 70%);
            top: 10%; left: 50%;
            animation: blobMove5 11s ease-in-out infinite;
        }


        @keyframes blobMove1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(40px, 30px) scale(1.1); }
            50% { transform: translate(20px, 60px) scale(0.95); }
            75% { transform: translate(-20px, 20px) scale(1.05); }
        }
        @keyframes blobMove2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(-30px, 40px) scale(1.08); }
            50% { transform: translate(-50px, -20px) scale(0.92); }
            75% { transform: translate(10px, -40px) scale(1.04); }
        }
        @keyframes blobMove3 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(40px, -30px) scale(1.12); }
            66% { transform: translate(-30px, -50px) scale(0.9); }
        }
        @keyframes blobMove4 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            30% { transform: translate(-40px, -25px) scale(1.15); }
            60% { transform: translate(30px, 20px) scale(0.88); }
        }
        @keyframes blobMove5 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            40% { transform: translate(-30px, 40px) scale(1.1); }
            70% { transform: translate(20px, -30px) scale(0.95); }
        }

        /* Glass texture overlay on left panel */
        .left-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 30% 20%, rgba(255,255,255,0.06) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 80%, rgba(255,255,255,0.04) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
        }

        /* ═══════════════════════════════════════════
           RIGHT PANEL — Clean white
           ═══════════════════════════════════════════ */
        .right-panel {
            flex: 1;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 52px 48px;
        }

        /* ─── Inputs ─── */
        .login-input {
            width: 100%;
            background: #F9FAFB;
            border: 1.5px solid #E5E7EB;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            color: #111827;
            outline: none;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }
        .login-input::placeholder { color: #9CA3AF; }
        .login-input:focus {
            border-color: #3B6CF6;
            box-shadow: 0 0 0 3px rgba(59,108,246,0.1);
            background: #fff;
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

    <div class="page-wrap">
        <div class="split-card">

            {{-- ═══════ LEFT — Animated fluid gradient ═══════ --}}
            <div class="left-panel">

                {{-- Fluid animated blobs --}}
                <div class="fluid-blob blob-1"></div>
                <div class="fluid-blob blob-2"></div>
                <div class="fluid-blob blob-3"></div>
                <div class="fluid-blob blob-4"></div>
                <div class="fluid-blob blob-5"></div>

                {{-- Logo --}}
                <div class="fade-up" style="position:relative; z-index:2;">
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
                <div class="fade-up" style="position:relative; z-index:2; animation-delay:0.12s;">
                    <p style="color:rgba(255,255,255,0.55); font-size:13px; font-weight:500; margin-bottom:14px;">
                        Plataforma educativa
                    </p>
                    <h2 style="color:#ffffff; font-size:70px; font-weight:800; line-height:1.12; letter-spacing:-0.025em;">
                        Sistema de <br>Gestion<br>
                        <span style="background: linear-gradient(135deg, rgba(255,255,255,1), rgba(180,210,255,0.8)); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">Academica</span>
                    </h2>
                </div>

                {{-- Footer --}}
                <div class="fade-up" style="position:relative; z-index:2; animation-delay:0.22s;">
                    <p style="color:rgba(255,255,255,0.3); font-size:11px;">
                        &copy; {{ date('Y') }} SIGEA — Todos los derechos reservados
                    </p>
                </div>

            </div>

            {{-- ═══════ RIGHT — Form ═══════ --}}
            <div class="right-panel">
                {{ $slot }}
            </div>

        </div>
    </div>

</body>
</html>
