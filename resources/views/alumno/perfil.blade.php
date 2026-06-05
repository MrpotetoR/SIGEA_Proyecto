<x-panel title="Mi Perfil" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    @if(!$alumno)
        <div
            class="bg-yellow-50 dark:bg-amber-900/30 border border-yellow-300 dark:border-amber-700 text-yellow-800 dark:text-amber-300 rounded-lg p-6 text-center">
            No se encontró información de alumno vinculada a tu cuenta.
        </div>
    @else
        <div class="space-y-6">

            {{-- Encabezado --}}
            <div
                class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-6 flex items-center gap-6">
                <div
                    class="w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-700 dark:text-blue-300 flex-shrink-0">
                    <x-icon name="user" class="w-10 h-10" />
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $alumno->nombre_completo }}</h2>
                    <p class="text-[#0606F0] font-mono text-sm mt-1">ID: {{ $alumno->id_alumno_publico }}</p>
                    <span
                        class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold
                            {{ $alumno->estatus === 'activo' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
            ($alumno->estatus === 'baja_temporal' ? 'bg-yellow-100 text-yellow-800 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                        {{ ucwords(str_replace('_', ' ', $alumno->estatus)) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Datos académicos --}}
                <div
                    class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-6">
                    <h3
                        class="text-base font-semibold text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 pb-3 mb-4 inline-flex items-center gap-2">
                        <x-icon name="book" class="w-5 h-5 text-blue-600" /> Datos Académicos</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Carrera</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                {{ $alumno->carrera?->nombre_carrera ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Cuatrimestre Actual</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                {{ $alumno->cuatrimestre_actual ?? '—' }}°</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Promedio General</dt>
                            <dd class="text-sm font-bold text-blue-700">{{ $alumno->promedio_general }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Tutor asignado --}}
                <div
                    class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-6">
                    <h3
                        class="text-base font-semibold text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 pb-3 mb-4 inline-flex items-center gap-2">
                        <x-icon name="academic" class="w-5 h-5 text-blue-600" /> Tutor Asignado</h3>
                    @if($alumno->tutor)
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Nombre</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $alumno->tutor->nombre }}
                                    {{ $alumno->tutor->apellidos }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Especialidad</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                    {{ $alumno->tutor->especialidad ?? '—' }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-sm text-gray-400 dark:text-gray-500">Sin tutor asignado.</p>
                    @endif
                </div>

                {{-- Cuenta del sistema --}}
                <div
                    class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-6">
                    <h3
                        class="text-base font-semibold text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 pb-3 mb-4 inline-flex items-center gap-2">
                        <x-icon name="shield" class="w-5 h-5 text-blue-600" /> Cuenta del Sistema</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Usuario</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                {{ $alumno->user?->name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Correo</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                {{ $alumno->user?->email ?? '—' }}</dd>
                        </div>
                    </dl>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-4">
                        * Para modificar tus datos personales, acude a Control Escolar.
                    </p>
                </div>

                {{-- Código QR del alumno --}}
                <div
                    class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 dark:shadow-gray-900/20 rounded-xl shadow p-6">
                    <h3
                        class="text-base font-semibold text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 pb-3 mb-4 inline-flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 4h2m2-4h2m-2 4v2m0-6h-2v2m-2 2h2v2" />
                        </svg>
                        Código QR
                    </h3>
                    <div class="flex flex-col items-center justify-center gap-3">
                        <div class="relative inline-block">
                            <div
                                class="relative bg-white p-4 rounded-xl border border-gray-200 dark:border-gray-600 shadow-sm">
                                <div id="alumno-qrcode" data-id="{{ $alumno->id_alumno_publico }}"></div>
                                {{-- Logo UDEA centrado sobre el QR (nivel de corrección H tolera hasta ~30% de oclusión)
                                --}}
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="bg-white rounded-full p-1.5 shadow-md ring-1 ring-gray-100"
                                        style="width: 46px; height: 46px;">
                                        <img id="alumno-qrcode-logo" src="{{ asset('images/logo-udea-azul.png') }}"
                                            alt="UDEA" class="w-full h-full object-contain" />
                                    </div>
                                </div>
                            </div>
                            {{-- Botón de descarga --}}
                            <button type="button" id="alumno-qrcode-download" title="Descargar QR como PNG"
                                aria-label="Descargar código QR"
                                class="absolute -right-3 -bottom-3 w-11 h-11 rounded-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 shadow-md hover:shadow-lg hover:bg-[#0606F0] hover:text-white dark:hover:bg-[#0606F0] text-[#04276B] dark:text-gray-200 flex items-center justify-center transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-[#0606F0]/40">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-[#0606F0] font-mono text-sm">{{ $alumno->id_alumno_publico }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 text-center">
                            Código QR del alumno {{ $alumno->nombre_completo }}.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    @endif

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
        <script>
            (function () {
                var el = document.getElementById('alumno-qrcode');
                if (!el || typeof QRCode === 'undefined') return;
                var id = el.dataset.id || '';
                if (!id) return;

                new QRCode(el, {
                    text: id,
                    width: 160,
                    height: 160,
                    colorDark: '#04276B',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });

                // ─── Descarga del QR como PNG (con logo UDEA incrustado) ───
                var btnDownload = document.getElementById('alumno-qrcode-download');
                var logoEl = document.getElementById('alumno-qrcode-logo');
                if (!btnDownload) return;

                btnDownload.addEventListener('click', function () {
                    // qrcodejs renderiza un <canvas> (preferido) o un <img> dentro del div
                    var qrCanvas = el.querySelector('canvas');
                    var qrImg = el.querySelector('img');
                    if (!qrCanvas && !qrImg) return;

                    // Tamaño final del PNG exportado (más alta resolución que en pantalla)
                    var SIZE = 600;
                    var PADDING = 40; // margen blanco alrededor del QR
                    var canvas = document.createElement('canvas');
                    canvas.width = SIZE;
                    canvas.height = SIZE;
                    var ctx = canvas.getContext('2d');

                    // Fondo blanco
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, SIZE, SIZE);

                    var qrSize = SIZE - PADDING * 2;

                    var drawLogoAndExport = function () {
                        // Círculo blanco detrás del logo
                        var cx = SIZE / 2;
                        var cy = SIZE / 2;
                        var logoBoxRadius = qrSize * 0.13; // ~13% del QR
                        ctx.save();
                        ctx.fillStyle = '#ffffff';
                        ctx.beginPath();
                        ctx.arc(cx, cy, logoBoxRadius + 6, 0, Math.PI * 2);
                        ctx.fill();
                        // Anillo gris muy sutil
                        ctx.lineWidth = 2;
                        ctx.strokeStyle = 'rgba(0,0,0,0.06)';
                        ctx.stroke();
                        ctx.restore();

                        var drawLogoImage = function (img) {
                            // Caja máxima disponible para el logo (cuadrado inscrito en el círculo blanco)
                            var maxBox = logoBoxRadius * 1.6;
                            // Preservar aspect ratio del logo original (estilo object-contain)
                            var natW = img.naturalWidth || img.width || 1;
                            var natH = img.naturalHeight || img.height || 1;
                            var ratio = natW / natH;
                            var drawW, drawH;
                            if (ratio >= 1) {
                                drawW = maxBox;
                                drawH = maxBox / ratio;
                            } else {
                                drawH = maxBox;
                                drawW = maxBox * ratio;
                            }
                            ctx.drawImage(img, cx - drawW / 2, cy - drawH / 2, drawW, drawH);
                            exportPng();
                        };

                        // Re-cargar el logo en un Image nuevo para evitar problemas de CORS/estado
                        var freshLogo = new Image();
                        freshLogo.crossOrigin = 'anonymous';
                        freshLogo.onload = function () { drawLogoImage(freshLogo); };
                        freshLogo.onerror = function () {
                            // Si el logo falla, exportar sin él
                            exportPng();
                        };
                        freshLogo.src = logoEl ? logoEl.src : '{{ asset('images/logo-udea-azul.png') }}';
                    };

                    var exportPng = function () {
                        try {
                            var link = document.createElement('a');
                            link.download = 'QR-' + id + '.png';
                            link.href = canvas.toDataURL('image/png');
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        } catch (e) {
                            console.error('Error exportando QR:', e);
                            alert('No fue posible descargar el QR. Intenta de nuevo.');
                        }
                    };

                    // Dibujar el QR primero, luego el logo encima
                    if (qrCanvas) {
                        ctx.drawImage(qrCanvas, PADDING, PADDING, qrSize, qrSize);
                        drawLogoAndExport();
                    } else {
                        var qrSource = new Image();
                        qrSource.crossOrigin = 'anonymous';
                        qrSource.onload = function () {
                            ctx.drawImage(qrSource, PADDING, PADDING, qrSize, qrSize);
                            drawLogoAndExport();
                        };
                        qrSource.onerror = function () {
                            alert('No fue posible generar el QR para descargar.');
                        };
                        qrSource.src = qrImg.src;
                    }
                });
            })();
        </script>
    @endpush
</x-panel>