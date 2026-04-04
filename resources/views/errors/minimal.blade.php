@php
    $errors_config = [
        403 => ['title' => 'Acceso denegado', 'message' => 'No tienes permisos para acceder a este apartado.', 'icon' => 'lock', 'actions' => ['home', 'back']],
        404 => ['title' => 'Pagina no encontrada', 'message' => 'El recurso que buscas no existe o fue modificado.', 'icon' => 'search', 'actions' => ['home', 'back']],
        419 => ['title' => 'Sesion expirada', 'message' => 'Tu sesion ha expirado. Inicia sesion nuevamente.', 'icon' => 'clock', 'actions' => ['login']],
        429 => ['title' => 'Demasiadas solicitudes', 'message' => 'Espera unos momentos antes de intentar de nuevo.', 'icon' => 'alert', 'actions' => ['back']],
        500 => ['title' => 'Error interno', 'message' => 'Algo salio mal. Intenta de nuevo en unos minutos.', 'icon' => 'warning', 'actions' => ['reload', 'home']],
        503 => ['title' => 'En mantenimiento', 'message' => 'Estaremos de vuelta muy pronto.', 'icon' => 'gear', 'actions' => ['reload']],
        
    ];
    $code = $code ?? 500;
    $cfg = $errors_config[$code] ?? $errors_config[500];
    $icons = [
        'lock' => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>',
        'search' => '<circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'alert' => '<circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/>',
        'warning' => '<path d="M12 9v2m0 4h.01M5.07 19H19a2.13 2.13 0 001.81-3.19l-6.93-12a2.13 2.13 0 00-3.76 0l-6.93 12A2.13 2.13 0 005.07 19z"/>',
        'gear' => '<path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/>',
    ];
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} — SIGEA</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            overflow: hidden;
            background: #0f2060
        }

        /* ═══ Animated background ═══ */
        .bg {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #0f2b7a 0%, #1a4fc7 25%, #2563eb 50%, #1e3faf 75%, #0f2060 100%);
            overflow: hidden
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            mix-blend-mode: screen;
            will-change: transform
        }

        .b1 {
            width: 500px;
            height: 500px;
            filter: blur(50px);
            background: radial-gradient(circle, rgba(56, 182, 255, 0.7) 0%, transparent 70%);
            top: -12%;
            left: -8%;
            animation: m1 5s ease-in-out infinite
        }

        .b2 {
            width: 450px;
            height: 450px;
            filter: blur(55px);
            background: radial-gradient(circle, rgba(255, 255, 255, 0.55) 0%, transparent 70%);
            top: 10%;
            right: 0%;
            animation: m2 6s ease-in-out infinite
        }

        .b3 {
            width: 550px;
            height: 550px;
            filter: blur(60px);
            background: radial-gradient(circle, rgba(100, 80, 255, 0.55) 0%, transparent 70%);
            bottom: -18%;
            left: 15%;
            animation: m3 7s ease-in-out infinite
        }

        .b4 {
            width: 350px;
            height: 350px;
            filter: blur(45px);
            background: radial-gradient(circle, rgba(130, 220, 255, 0.6) 0%, transparent 70%);
            top: 50%;
            left: 3%;
            animation: m4 4.5s ease-in-out infinite
        }

        .b5 {
            width: 400px;
            height: 400px;
            filter: blur(50px);
            background: radial-gradient(circle, rgba(160, 100, 255, 0.5) 0%, transparent 70%);
            top: 5%;
            left: 45%;
            animation: m5 5.5s ease-in-out infinite
        }

        .b6 {
            width: 320px;
            height: 320px;
            filter: blur(45px);
            background: radial-gradient(circle, rgba(0, 200, 255, 0.5) 0%, transparent 70%);
            bottom: 8%;
            right: 12%;
            animation: m6 6.5s ease-in-out infinite
        }

        .b7 {
            width: 280px;
            height: 280px;
            filter: blur(40px);
            background: radial-gradient(circle, rgba(255, 255, 255, 0.4) 0%, transparent 60%);
            top: 55%;
            left: 50%;
            animation: m7 4s ease-in-out infinite
        }

        @keyframes m1 {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            25% {
                transform: translate(60px, 50px) scale(1.15)
            }

            50% {
                transform: translate(30px, 90px) scale(.9)
            }

            75% {
                transform: translate(-30px, 30px) scale(1.08)
            }
        }

        @keyframes m2 {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            20% {
                transform: translate(-50px, 60px) scale(1.12)
            }

            50% {
                transform: translate(-80px, -30px) scale(.88)
            }

            80% {
                transform: translate(20px, -50px) scale(1.06)
            }
        }

        @keyframes m3 {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            30% {
                transform: translate(70px, -50px) scale(1.18)
            }

            60% {
                transform: translate(-40px, -80px) scale(.85)
            }
        }

        @keyframes m4 {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            35% {
                transform: translate(-60px, -40px) scale(1.2)
            }

            65% {
                transform: translate(50px, 30px) scale(.85)
            }
        }

        @keyframes m5 {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            30% {
                transform: translate(-40px, 60px) scale(1.15)
            }

            70% {
                transform: translate(35px, -45px) scale(.9)
            }
        }

        @keyframes m6 {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            40% {
                transform: translate(-50px, -40px) scale(1.1)
            }

            75% {
                transform: translate(30px, 50px) scale(.92)
            }
        }

        @keyframes m7 {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            50% {
                transform: translate(40px, -35px) scale(1.18)
            }
        }

        /* ═══ Layout ═══ */
        .ct {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            text-align: center
        }

        /* ═══ Content — no card, just floating elements ═══ */
        .inner {
            max-width: 400px;
            width: 100%;
            animation: slideUp .7s cubic-bezier(.16, 1, .3, 1) forwards;
            opacity: 0.7;
            transform: translateY(24px);
        }

        /* Icon */
        .ic {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 32px;
        }

        /* Number */
        .num {
            font-size: 140px;
            font-weight: 900;
            letter-spacing: -0.06em;
            line-height: 0.85;
            margin-bottom: 16px;
            color: rgba(255, 255, 255, 0.45);
        }

        /* Title */
        .tt {
            font-size: 18px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 10px;
            letter-spacing: -0.01em;
        }

        /* Message */
        .msg {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.6;
            margin-bottom: 40px;
        }

        /* Divider */
        .divider {
            width: 32px;
            height: 1px;
            background: rgba(255, 255, 255, 0.15);
            margin: 0 auto 32px;
        }

        /* Buttons */
        .btns {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 22px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.8);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
            transition: all .25s ease;
            letter-spacing: 0.01em;
        }

        .btn:hover {
            background: rgba(255, 255, 255, 0.92);
            color: #1a3a8a;
            transform: translateY(-1px);
        }

        .btn-p {
            background: rgba(255, 255, 255, 0.92);
            color: #1a3a8a;
            border-color: transparent;
            font-weight: 600;
        }

        .btn-p:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 4px 20px rgba(255, 255, 255, 0.2);
        }

        /* Logo */
        .logo {
            position: fixed;
            top: 28px;
            left: 32px;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: fi .5s ease forwards;
            opacity: 0;
        }

        .logo-i {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        @keyframes fi {
            to {
                opacity: 1
            }
        }

        @media(max-width:480px) {
            .num {
                font-size: 100px
            }

            .btns {
                flex-direction: column;
                align-items: center
            }
        }
    </style>
</head>

<body>
    <div class="bg">
        <div class="blob b1"></div>
        <div class="blob b2"></div>
        <div class="blob b3"></div>
        <div class="blob b4"></div>
        <div class="blob b5"></div>
        <div class="blob b6"></div>
        <div class="blob b7"></div>
    </div>

    <div class="logo">
        <div class="logo-i">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(255, 255, 255, 0.81)"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>
        <span style="color:rgba(255,255,255,0.6);font-size:14px;font-weight:600">SIGEA</span>
    </div>

    <div class="ct">
        <div class="inner">
            <div class="ic">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(255, 255, 255, 0.78)"
                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">{!! $icons[$cfg['icon']] !!}</svg>
            </div>
            <div class="num">{{ $code }}</div>
            <div class="tt">{{ $cfg['title'] }}</div>
            <div class="msg">{{ $cfg['message'] }}</div>
            <div class="divider"></div>
            <div class="btns">
                @foreach($cfg['actions'] as $i => $action)
                    @if($action === 'home')
                        <a href="{{ url('/') }}" class="btn {{ $i === 0 ? 'btn-p' : '' }}">Ir al inicio</a>
                    @elseif($action === 'back')
                        <a href="javascript:history.back()" class="btn {{ $i === 0 ? 'btn-p' : '' }}">Regresar</a>
                    @elseif($action === 'login')
                        <a href="{{ route('login') }}" class="btn btn-p">Iniciar sesion</a>
                    @elseif($action === 'reload')
                        <a href="javascript:location.reload()" class="btn {{ $i === 0 ? 'btn-p' : '' }}">Reintentar</a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</body>

</html>