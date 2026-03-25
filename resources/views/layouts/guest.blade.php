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
        body { font-family: 'Inter', system-ui, sans-serif; background: #000; color: #fff; }

        /* ─── Left panel ─── */
        .left-panel {
            background: #0a0a0a;
            position: relative;
            overflow: hidden;
        }

        /* Subtle grid lines behind asterisk */
        .grid-lines {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 80px 80px;
        }

        /* Diagonal cross lines across the panel — replicates the X in the reference */
        .cross-lines {
            position: absolute;
            inset: 0;
            overflow: hidden;
        }
        .cross-lines::before,
        .cross-lines::after {
            content: '';
            position: absolute;
            background: rgba(255,255,255,0.06);
            width: 1px;
            height: 200%;
            top: -50%;
            left: 50%;
            transform-origin: center center;
        }
        .cross-lines::before { transform: rotate(35deg); }
        .cross-lines::after  { transform: rotate(-35deg); }

        /* ─── Asterisk icon ─── */
        .asterisk-wrap {
            position: relative;
            z-index: 10;
        }

        /* ─── Right panel (form area) ─── */
        .right-panel {
            background: #111;
            position: relative;
        }

        /* ─── Input: line-only style ─── */
        .line-input {
            background: transparent;
            border: none;
            border-bottom: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            font-size: 14px;
            padding: 10px 0;
            width: 100%;
            outline: none;
            transition: border-color 0.2s ease;
            font-family: 'Inter', sans-serif;
        }
        .line-input::placeholder { color: rgba(255,255,255,0.25); }
        .line-input:focus { border-bottom-color: rgba(255,255,255,0.8); }
        .line-input option { background: #1a1a1a; color: #fff; }

        /* ─── Label ─── */
        .field-label {
            display: block;
            font-size: 11px;
            font-weight: 500;
            color: rgba(255,255,255,0.45);
            letter-spacing: 0.04em;
            margin-bottom: 4px;
        }

        /* ─── Sign In button ─── */
        .signin-btn {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #fff;
            color: #000;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease, transform 0.15s ease;
            line-height: 1.2;
            text-align: center;
        }
        .signin-btn:hover { background: #e5e5e5; transform: scale(1.04); }
        .signin-btn:active { transform: scale(0.97); }

        /* ─── Select arrow override ─── */
        select.line-input {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='rgba(255,255,255,0.4)' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 4px center;
            padding-right: 24px;
            cursor: pointer;
        }

        /* ─── Password toggle button ─── */
        .pass-toggle {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.35);
            display: flex;
            align-items: center;
            transition: color 0.15s;
        }
        .pass-toggle:hover { color: rgba(255,255,255,0.7); }

        /* ─── Fade-in ─── */
        .fade-in {
            animation: fadeIn 0.5s ease forwards;
            opacity: 0;
        }
        @keyframes fadeIn { to { opacity: 1; } }

        /* ─── Shake for errors ─── */
        .shake { animation: shake 0.45s cubic-bezier(.36,.07,.19,.97) both; }
        @keyframes shake {
            10%, 90% { transform: translateX(-1px); }
            20%, 80% { transform: translateX(2px); }
            30%, 50%, 70% { transform: translateX(-3px); }
            40%, 60% { transform: translateX(3px); }
        }

        ::-webkit-scrollbar { width: 0; }
    </style>
</head>
<body class="min-h-screen">

    <div class="flex min-h-screen">

        {{-- ════════ LEFT PANEL ════════ --}}
        <div class="hidden lg:flex lg:w-[52%] left-panel flex-col justify-between p-10 relative">

            {{-- Background layers --}}
            <div class="grid-lines"></div>
            <div class="cross-lines"></div>

            {{-- Top: Brand --}}
            <div class="relative z-10">
                <span class="text-white text-[15px] font-bold tracking-wide">SIGEA<sup class="text-[9px] font-normal opacity-60 ml-0.5">®</sup></span>
            </div>

            {{-- Center: Asterisk icon --}}
            <div class="relative z-10 flex items-center justify-center">
                <div class="asterisk-wrap">
                    <svg width="180" height="180" viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                        {{-- 8-spoke asterisk --}}
                        <line x1="90" y1="12" x2="90" y2="168" stroke="white" stroke-width="1.5"/>
                        <line x1="12" y1="90" x2="168" y2="90" stroke="white" stroke-width="1.5"/>
                        <line x1="31.6" y1="31.6" x2="148.4" y2="148.4" stroke="white" stroke-width="1.5"/>
                        <line x1="148.4" y1="31.6" x2="31.6" y2="148.4" stroke="white" stroke-width="1.5"/>
                    </svg>
                </div>
            </div>

            {{-- Bottom: Copyright --}}
            <div class="relative z-10">
                <p class="text-[11px] text-white/25 font-normal">&copy; SIGEA {{ date('Y') }}. Todos los derechos reservados</p>
            </div>

        </div>

        {{-- ════════ RIGHT PANEL — Formulario ════════ --}}
        <div class="flex-1 right-panel flex flex-col">

            {{-- Top bar --}}
            <div class="flex justify-end px-10 py-8">
                {{-- Placeholder para accion superior si se necesita --}}
            </div>

            {{-- Form centered --}}
            <div class="flex-1 flex items-center justify-center px-8 sm:px-16 lg:px-20 xl:px-28">
                <div class="w-full max-w-[380px] fade-in">
                    {{ $slot }}
                </div>
            </div>

            {{-- Bottom spacer --}}
            <div class="py-8"></div>
        </div>

    </div>

</body>
</html>
