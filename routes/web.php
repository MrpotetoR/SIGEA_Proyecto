<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Redirigir raíz según rol del usuario
Route::get('/', function () {
    if (auth()->check()) {
        return redirect(auth()->user()->panelUrl());
    }
    return redirect()->route('login');
});

// Dashboard genérico - redirige al panel correcto
Route::get('/dashboard', function () {
    return redirect(auth()->user()->panelUrl());
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cambiar contraseña (accesible para todos los roles)
    Route::get('/cambiar-password',  [\App\Http\Controllers\CambiarPasswordController::class, 'index'])->name('cambiar-password');
    Route::put('/cambiar-password',  [\App\Http\Controllers\CambiarPasswordController::class, 'update'])->name('cambiar-password.update');

    // ── Vista universal de noticia (accesible a cualquier rol autenticado,
    //    la autorización por destinatarios se valida dentro del controller). ──
    Route::get('/noticias/{noticia}', [\App\Http\Controllers\NoticiaController::class, 'show'])
        ->name('noticias.show');

    // ── Notificaciones API ──
    Route::prefix('notificaciones')->name('notificaciones.')->group(function () {
        Route::get('/',           [\App\Http\Controllers\NotificacionController::class, 'index'])->name('index');
        Route::post('/{notificacion}/leida', [\App\Http\Controllers\NotificacionController::class, 'marcarLeida'])->name('leida');
        Route::post('/marcar-todas', [\App\Http\Controllers\NotificacionController::class, 'marcarTodasLeidas'])->name('todas-leidas');
    });

    // ── Chatbot: reset de historial de conversacion (compartido para los 4 roles).
    //    Borra la entrada `chatbot_historial` de la sesion actual; al ser por
    //    sesion no afecta a otros usuarios.
    Route::post('/chatbot/reset',
        [\App\Http\Controllers\ChatbotController::class, 'resetHistorial'])
        ->name('chatbot.reset');
});

// ============================================================
// AJAX SEARCH (compartido entre paneles)
// ============================================================
Route::middleware('auth')->prefix('ajax')->name('ajax.')->group(function () {
    Route::get('/alumnos',  [\App\Http\Controllers\AjaxSearchController::class, 'alumnos'])->name('alumnos');
    Route::get('/docentes', [\App\Http\Controllers\AjaxSearchController::class, 'docentes'])->name('docentes');
    Route::get('/grupos',   [\App\Http\Controllers\AjaxSearchController::class, 'grupos'])->name('grupos');
});

// ============================================================
// TIENDA INSTITUCIONAL (alumno + docente)
// ============================================================
Route::middleware(['auth', 'verified', 'role:alumno|docente'])
    ->prefix('tienda')->name('tienda.')
    ->group(function () {
        Route::get('/',                  [\App\Http\Controllers\TiendaController::class, 'catalogo'])->name('catalogo');
        Route::get('/producto/{producto}', [\App\Http\Controllers\TiendaController::class, 'detalle'])->name('detalle');

        // Carrito (sesion)
        Route::get('/carrito',           [\App\Http\Controllers\TiendaController::class, 'carrito'])->name('carrito');
        Route::post('/carrito',          [\App\Http\Controllers\TiendaController::class, 'agregarAlCarrito'])->name('carrito.agregar');
        Route::patch('/carrito/{idVariante}', [\App\Http\Controllers\TiendaController::class, 'actualizarCarrito'])->name('carrito.actualizar');
        Route::post('/carrito/vaciar',   [\App\Http\Controllers\TiendaController::class, 'vaciarCarrito'])->name('carrito.vaciar');

        // Checkout
        Route::get('/checkout',          [\App\Http\Controllers\TiendaController::class, 'checkout'])->name('checkout');
        Route::post('/checkout/confirmar', [\App\Http\Controllers\TiendaController::class, 'confirmarPedido'])->name('pedido.confirmar');

        // Mis pedidos
        Route::get('/pedidos',           [\App\Http\Controllers\TiendaController::class, 'pedidos'])->name('pedidos');
        Route::get('/pedidos/{pedido}',  [\App\Http\Controllers\TiendaController::class, 'verPedido'])->name('pedido.show');
        Route::post('/pedidos/{pedido}/vaucher',  [\App\Http\Controllers\TiendaController::class, 'subirVaucher'])->name('pedido.vaucher');
        Route::post('/pedidos/{pedido}/cancelar', [\App\Http\Controllers\TiendaController::class, 'cancelarPedido'])->name('pedido.cancelar');
        Route::get('/pedidos/{pedido}/comprobante.pdf', [\App\Http\Controllers\TiendaController::class, 'comprobantePdf'])->name('pedido.comprobante');
    });

// ============================================================
// PANEL ALUMNO
// ============================================================
Route::prefix('alumno')->name('alumno.')->middleware(['auth', 'verified', 'role:alumno'])->group(function () {
    Route::get('/dashboard',        [\App\Http\Controllers\Alumno\DashboardController::class,          'index'])->name('dashboard');
    Route::get('/perfil',           [\App\Http\Controllers\Alumno\PerfilController::class,             'index'])->name('perfil');
    Route::get('/horario',          [\App\Http\Controllers\Alumno\HorarioController::class,            'index'])->name('horario');
    Route::get('/calificaciones',   [\App\Http\Controllers\Alumno\CalificacionesController::class,     'index'])->name('calificaciones');
    Route::get('/kardex',           [\App\Http\Controllers\Alumno\KardexController::class,             'index'])->name('kardex');
    Route::get('/kardex/pdf',       [\App\Http\Controllers\Alumno\KardexController::class,             'descargarPdf'])->name('kardex.pdf');
    Route::get('/historial',        [\App\Http\Controllers\Alumno\HistorialAcademicoController::class, 'index'])->name('historial');
    Route::get('/servicio-social',  [\App\Http\Controllers\Alumno\ServicioSocialController::class,    'index'])->name('servicio-social');
    Route::get('/evaluacion-docente',  [\App\Http\Controllers\Alumno\EvaluacionDocenteController::class, 'index'])->name('evaluacion-docente');
    Route::post('/evaluacion-docente', [\App\Http\Controllers\Alumno\EvaluacionDocenteController::class, 'store'])->name('evaluacion-docente.store');
    Route::get('/mis-docentes',     [\App\Http\Controllers\Alumno\DocentesController::class,          'index'])->name('mis-docentes');
    Route::get('/noticias',         [\App\Http\Controllers\Alumno\NoticiasController::class,          'index'])->name('noticias');
    Route::get('/pagos',            [\App\Http\Controllers\Alumno\PagosController::class,            'index'])->name('pagos');
    Route::post('/pagos',           [\App\Http\Controllers\Alumno\PagosController::class,            'store'])->name('pagos.store');
    Route::post('/chatbot',         [\App\Http\Controllers\ChatbotController::class,                  'responder'])->name('chatbot');
});

// ============================================================
// PANEL DOCENTE
// ============================================================
Route::prefix('docente')->name('docente.')->middleware(['auth', 'verified', 'role:docente'])->group(function () {
    Route::get('/dashboard',          [\App\Http\Controllers\Docente\DashboardController::class,          'index'])->name('dashboard');
    Route::get('/perfil',             [\App\Http\Controllers\Docente\PerfilController::class,             'index'])->name('perfil');
    Route::get('/grupos',             [\App\Http\Controllers\Docente\GruposController::class,             'index'])->name('grupos');
    Route::get('/horario',            [\App\Http\Controllers\Docente\HorarioController::class,            'index'])->name('horario');
    Route::get('/tutorados',          [\App\Http\Controllers\Docente\TutoradosController::class,          'index'])->name('tutorados');

    // Asistencia
    Route::get('/asistencia',                     [\App\Http\Controllers\Docente\AsistenciaController::class, 'index'])->name('asistencia');
    Route::get('/asistencia/{grupo}',             [\App\Http\Controllers\Docente\AsistenciaController::class, 'show'])->name('asistencia.show');
    Route::get('/asistencia/{grupo}/historial',   [\App\Http\Controllers\Docente\AsistenciaController::class, 'historial'])->name('asistencia.historial');
    Route::post('/asistencia/{grupo}',            [\App\Http\Controllers\Docente\AsistenciaController::class, 'store'])->name('asistencia.store');

    // Calificaciones
    Route::get('/calificaciones',           [\App\Http\Controllers\Docente\CalificacionesController::class, 'index'])->name('calificaciones');
    Route::get('/calificaciones/{grupo}',   [\App\Http\Controllers\Docente\CalificacionesController::class, 'show'])->name('calificaciones.show');
    Route::post('/calificaciones/{grupo}',  [\App\Http\Controllers\Docente\CalificacionesController::class, 'store'])->name('calificaciones.store');
    Route::put('/calificaciones/{calificacion}', [\App\Http\Controllers\Docente\CalificacionesController::class, 'update'])->name('calificaciones.update');

    // Reportes
    Route::get('/reporte-asistencia',   [\App\Http\Controllers\Docente\ReporteAsistenciaController::class,  'index'])->name('reporte-asistencia');
    Route::get('/reporte-rendimiento',  [\App\Http\Controllers\Docente\ReporteRendimientoController::class, 'index'])->name('reporte-rendimiento');

    // Horas ACUDE

    // Servicio Social
    // Servicio Social: movido al rol Gestor Escolar (decision institucional UDEA).


    // Evaluación y Noticias
    Route::get('/evaluacion-resultados', [\App\Http\Controllers\Docente\EvaluacionResultadosController::class, 'index'])->name('evaluacion-resultados');
    Route::get('/noticias',              [\App\Http\Controllers\Docente\NoticiasController::class,              'index'])->name('noticias');

    // Chatbot
    Route::post('/chatbot', [\App\Http\Controllers\ChatbotController::class, 'responder'])->name('chatbot');
});

// ============================================================
// PANEL ADMIN
// ============================================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Personal de Gestor Escolar (CRUD).
    Route::resource('personal', \App\Http\Controllers\Admin\PersonalController::class);

    // Historial de asignaciones de carrera (auditoría).
    Route::get('/personal-historial',
        [\App\Http\Controllers\Admin\HistorialAsignacionesController::class, 'index'])
        ->name('personal.historial');

    // Asignación de carreras a personal.
    Route::get('/asignaciones',           [\App\Http\Controllers\Admin\AsignacionCarreraController::class, 'index'])->name('asignaciones.index');
    Route::post('/asignaciones',          [\App\Http\Controllers\Admin\AsignacionCarreraController::class, 'store'])->name('asignaciones.store');
    Route::delete('/asignaciones',        [\App\Http\Controllers\Admin\AsignacionCarreraController::class, 'destroy'])->name('asignaciones.destroy');
    Route::post('/asignaciones/transfer', [\App\Http\Controllers\Admin\AsignacionCarreraController::class, 'transfer'])->name('asignaciones.transfer');

    // Administradores (otros admins).
    Route::resource('administradores', \App\Http\Controllers\Admin\AdministradoresController::class)
        ->except(['show']);

    // Reportes consolidados (Universidad + Bachillerato).
    Route::get('/reportes-consolidados',
        [\App\Http\Controllers\Admin\ReportesConsolidadosController::class, 'index'])
        ->name('reportes-consolidados');

    // Configuración de la Tienda Institucional (cuenta bancaria, ubicación entrega).
    Route::get('/configuracion/tienda',
        [\App\Http\Controllers\Admin\ConfiguracionTiendaController::class, 'edit'])
        ->name('configuracion.tienda');
    Route::put('/configuracion/tienda',
        [\App\Http\Controllers\Admin\ConfiguracionTiendaController::class, 'update'])
        ->name('configuracion.tienda.update');

    // ============================================================
    // CAJA CHICA — Fondo de emergencia (admin)
    // ============================================================
    Route::get('/caja-chica/fondo',
        [\App\Http\Controllers\Admin\CajaChicaFondoController::class, 'edit'])
        ->name('caja-chica.fondo.edit');
    Route::put('/caja-chica/fondo',
        [\App\Http\Controllers\Admin\CajaChicaFondoController::class, 'update'])
        ->name('caja-chica.fondo.update');
    Route::post('/caja-chica/fondo/repone',
        [\App\Http\Controllers\Admin\CajaChicaFondoController::class, 'repone'])
        ->name('caja-chica.fondo.repone');
    // Historial completo del módulo Caja Chica (movimientos del fondo + vales + permisos).
    Route::get('/caja-chica/historial',
        [\App\Http\Controllers\Admin\HistorialCajaChicaController::class, 'index'])
        ->name('caja-chica.historial');

    // Correos adicionales para notificaciones de Caja Chica (máx 3 por admin).
    Route::get('/caja-chica/correos',
        [\App\Http\Controllers\Admin\NotificacionesCorreosController::class, 'index'])
        ->name('caja-chica.correos.index');
    Route::post('/caja-chica/correos',
        [\App\Http\Controllers\Admin\NotificacionesCorreosController::class, 'store'])
        ->name('caja-chica.correos.store');
    Route::patch('/caja-chica/correos/{correo}/toggle',
        [\App\Http\Controllers\Admin\NotificacionesCorreosController::class, 'toggle'])
        ->name('caja-chica.correos.toggle');
    Route::delete('/caja-chica/correos/{correo}',
        [\App\Http\Controllers\Admin\NotificacionesCorreosController::class, 'destroy'])
        ->name('caja-chica.correos.destroy');

    // ============================================================
    // CAJA GENERAL — Dashboard, reportes y cobros de trámites
    // ============================================================
    // Dashboard + reportes
    Route::get('/caja-general',
        [\App\Http\Controllers\Admin\CajaGeneralController::class, 'index'])
        ->name('caja-general.index');
    Route::get('/caja-general/consolidado',
        [\App\Http\Controllers\Admin\CajaGeneralController::class, 'consolidado'])
        ->name('caja-general.consolidado');
    Route::get('/caja-general/export/pdf',
        [\App\Http\Controllers\Admin\CajaGeneralController::class, 'exportPdf'])
        ->name('caja-general.export-pdf');
    Route::get('/caja-general/export/csv',
        [\App\Http\Controllers\Admin\CajaGeneralController::class, 'exportCsv'])
        ->name('caja-general.export-csv');

    // Cobros manuales de trámites (kárdex, constancias, etc.)
    Route::get('/caja-general/tramites',
        [\App\Http\Controllers\Admin\CobroTramiteController::class, 'index'])
        ->name('caja-general.cobro-tramite.index');
    Route::get('/caja-general/tramites/nuevo',
        [\App\Http\Controllers\Admin\CobroTramiteController::class, 'create'])
        ->name('caja-general.cobro-tramite.create');
    Route::post('/caja-general/tramites',
        [\App\Http\Controllers\Admin\CobroTramiteController::class, 'store'])
        ->name('caja-general.cobro-tramite.store');
    Route::post('/caja-general/tramites/{cobro}/cancelar',
        [\App\Http\Controllers\Admin\CobroTramiteController::class, 'cancelar'])
        ->name('caja-general.cobro-tramite.cancelar');
    Route::get('/caja-general/tramites/alumnos/buscar',
        [\App\Http\Controllers\Admin\CobroTramiteController::class, 'buscarAlumnos'])
        ->name('caja-general.cobro-tramite.alumnos');

    // Chatbot (compartido).
    Route::post('/chatbot', [\App\Http\Controllers\ChatbotController::class, 'responder'])->name('chatbot');
});

// ============================================================
// REAUTH — accesible para admin y gestor_escolar (acciones sensibles
// como crear carrera, asignar carreras, otorgar permiso especial, etc.)
// ============================================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin|gestor_escolar'])->group(function () {
    Route::post('/reauth', [\App\Http\Controllers\Admin\ReauthController::class, 'verificar'])
        ->name('reauth');
    Route::get('/reauth/estado', [\App\Http\Controllers\Admin\ReauthController::class, 'estado'])
        ->name('reauth.estado');
});

// ============================================================
// PANEL GESTOR ESCOLAR (fusion de Servicios Escolares + Director de Carrera)
//
// El middleware "contexto.educativo" se encarga de:
//   1. Validar que el usuario tenga al menos un nivel asignado (universidad/bachillerato).
//   2. Auto-seleccionar el nivel cuando solo hay uno disponible.
//   3. Redirigir al selector cuando el usuario tiene ambos.
//   4. Compartir $contextoActual, $contextoDisponibles, $contextoColor con las vistas.
// ============================================================

// Pantalla de seleccion + cambio de contexto (NO requiere contexto previo).
Route::prefix('gestor-escolar')->name('gestor.contexto.')->middleware(['auth', 'verified', 'role:gestor_escolar'])->group(function () {
    Route::get('/seleccionar-area', [\App\Http\Controllers\Gestor\ContextoController::class, 'seleccionar'])->name('seleccionar');
    Route::post('/cambiar-area',    [\App\Http\Controllers\Gestor\ContextoController::class, 'cambiar'])->name('cambiar');
});

Route::prefix('gestor-escolar')->name('gestor.')->middleware(['auth', 'verified', 'role:gestor_escolar', 'contexto.educativo'])->group(function () {

    // Dashboard y perfil
    Route::get('/dashboard', [\App\Http\Controllers\Gestor\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/perfil',    [\App\Http\Controllers\Gestor\PerfilController::class,    'index'])->name('perfil');

    // Alumnos (CRUD completo + bajas/reingresos/pagos)
    Route::resource('alumnos', \App\Http\Controllers\Gestor\AlumnosController::class);
    Route::delete('/alumnos/documentos/{documento}', [\App\Http\Controllers\Gestor\AlumnosController::class, 'eliminarDocumento'])->name('alumnos.documentos.destroy');
    Route::post('/alumnos/{alumno}/baja',       [\App\Http\Controllers\Gestor\AlumnosController::class, 'registrarBaja'])->name('alumnos.baja');
    Route::post('/alumnos/{alumno}/reingreso',  [\App\Http\Controllers\Gestor\AlumnosController::class, 'registrarReingreso'])->name('alumnos.reingreso');
    Route::post('/alumnos/{alumno}/baucher',    [\App\Http\Controllers\Gestor\AlumnosController::class, 'subirBaucher'])->name('alumnos.baucher');
    Route::post('/pagos/{pago}/aprobar',  [\App\Http\Controllers\Gestor\AlumnosController::class, 'aprobarBaucher'])->name('pagos.aprobar');
    Route::post('/pagos/{pago}/rechazar', [\App\Http\Controllers\Gestor\AlumnosController::class, 'rechazarBaucher'])->name('pagos.rechazar');

    // Historial academico de alumnos (heredado de Director)
    Route::get('/historial-alumnos',                  [\App\Http\Controllers\Gestor\HistorialAlumnosController::class, 'index'])->name('historial-alumnos.index');
    Route::get('/historial-alumnos/{alumno}',         [\App\Http\Controllers\Gestor\HistorialAlumnosController::class, 'show'])->name('historial-alumnos.show');

    // Docentes (CRUD)
    Route::resource('docentes', \App\Http\Controllers\Gestor\DocentesController::class);
    Route::delete('/docentes/documentos/{documento}', [\App\Http\Controllers\Gestor\DocentesController::class, 'eliminarDocumento'])->name('docentes.documentos.destroy');

    // ====== Rutas SOLO Universidad ======
    Route::middleware('contexto.solo:universidad')->group(function () {
        // Carreras (Bachillerato usa "planes-bachillerato" en su lugar)
        Route::resource('carreras', \App\Http\Controllers\Gestor\CarrerasController::class);
        // Asignar/reasignar carrera a un gestor (acción sensible: reauth + motivo + log).
        Route::post('carreras/{carrera}/asignar',
            [\App\Http\Controllers\Gestor\CarrerasController::class, 'asignar'])
            ->name('carreras.asignar');
        // Plan de Estudios (Universidad)
        Route::get('/plan-estudios', [\App\Http\Controllers\Gestor\PlanEstudiosController::class, 'index'])->name('plan-estudios');
    });

    // ====== Rutas SOLO Bachillerato ======
    Route::middleware('contexto.solo:bachillerato')->group(function () {
        Route::resource('planes-bachillerato', \App\Http\Controllers\Gestor\BachilleratoPlanesController::class)
            ->parameters(['planes-bachillerato' => 'plan'])
            ->except(['show']);
    });

    // Materias y ciclos: aplican en ambos contextos (el formulario se adapta)
    Route::resource('materias', \App\Http\Controllers\Gestor\MateriasController::class);
    // Ciclos: creación SOLO vía batch (crear-anio). Sin create/store individual — regla UDEA:
    // todos los ciclos siguen una de las dos plantillas (cuatrimestral / semestral).
    Route::post('ciclos/crear-anio', [\App\Http\Controllers\Gestor\CiclosController::class, 'crearAnio'])->name('ciclos.crear-anio');
    Route::resource('ciclos', \App\Http\Controllers\Gestor\CiclosController::class)
        ->except(['create', 'store']);

    // Grupos (CRUD + inscripcion individual)
    Route::resource('grupos', \App\Http\Controllers\Gestor\GruposController::class);
    Route::post('grupos/{grupo}/inscribir',                [\App\Http\Controllers\Gestor\GruposController::class, 'inscribir'])->name('grupos.inscribir');
    Route::delete('grupos/{grupo}/desinscribir/{alumno}',  [\App\Http\Controllers\Gestor\GruposController::class, 'desinscribir'])->name('grupos.desinscribir');

    // Horarios (CRUD)
    Route::resource('horarios', \App\Http\Controllers\Gestor\HorariosController::class);

    // Inscripciones masivas
    Route::get('/inscripciones',                  [\App\Http\Controllers\Gestor\InscripcionesController::class, 'index'])->name('inscripciones');
    Route::get('/inscripciones/check',            [\App\Http\Controllers\Gestor\InscripcionesController::class, 'check'])->name('inscripciones.check');
    Route::get('/inscripciones/promover',         [\App\Http\Controllers\Gestor\InscripcionesController::class, 'promoverForm'])->name('inscripciones.promover.form');
    Route::get('/inscripciones/promover/preview', [\App\Http\Controllers\Gestor\InscripcionesController::class, 'previewPromocion'])->name('inscripciones.promover.preview');
    Route::post('/inscripciones/promover',        [\App\Http\Controllers\Gestor\InscripcionesController::class, 'promover'])->name('inscripciones.promover');
    Route::post('/inscripciones',                 [\App\Http\Controllers\Gestor\InscripcionesController::class, 'store'])->name('inscripciones.store');
    Route::delete('/inscripciones/{inscripcion}', [\App\Http\Controllers\Gestor\InscripcionesController::class, 'destroy'])->name('inscripciones.destroy');

    // Constancias
    Route::get('/constancias',                    [\App\Http\Controllers\Gestor\ConstanciasController::class, 'index'])->name('constancias');
    Route::post('/constancias',                   [\App\Http\Controllers\Gestor\ConstanciasController::class, 'store'])->name('constancias.store');
    Route::get('/constancias/{constancia}/pdf',   [\App\Http\Controllers\Gestor\ConstanciasController::class, 'pdf'])->name('constancias.pdf');

    // Servicio Social (movido del rol Docente al Gestor Escolar)
    Route::resource('servicio-social', \App\Http\Controllers\Gestor\ServicioSocialController::class)
        ->parameters(['servicio-social' => 'servicioSocial']);

    // Asistencia, indices, evaluaciones (heredados de Director)
    Route::get('/asistencia',          [\App\Http\Controllers\Gestor\AsistenciaController::class,          'index'])->name('asistencia');
    Route::get('/indice-aprobacion',   [\App\Http\Controllers\Gestor\IndiceAprobacionController::class,   'index'])->name('indice-aprobacion');
    Route::get('/evaluacion-docente',  [\App\Http\Controllers\Gestor\EvaluacionDocenteController::class,  'index'])->name('evaluacion-docente');

    // Noticias, documentos, reportes
    Route::resource('noticias',   \App\Http\Controllers\Gestor\NoticiasController::class);
    // Alias legacy del index de documentos: redirige a la vista unificada con tab=documentos.
    Route::get('/documentos', fn() => redirect()->route('gestor.documentacion-reportes', ['tab' => 'documentos']))
        ->name('documentos.index');
    Route::resource('documentos', \App\Http\Controllers\Gestor\DocumentosController::class)
        ->except(['index']);

    // Vista unificada: Documentación y Reportes (tabs: reportes | documentos).
    Route::get('/documentacion-reportes',
        [\App\Http\Controllers\Gestor\DocumentacionReportesController::class, 'index'])
        ->name('documentacion-reportes');

    // Carpetas dentro de Documentos Institucionales.
    Route::post('/documentos/carpetas',
        [\App\Http\Controllers\Gestor\CarpetasDocumentoController::class, 'store'])
        ->name('documentos.carpetas.store');
    Route::put('/documentos/carpetas/{carpeta}',
        [\App\Http\Controllers\Gestor\CarpetasDocumentoController::class, 'update'])
        ->name('documentos.carpetas.update');
    Route::delete('/documentos/carpetas/{carpeta}',
        [\App\Http\Controllers\Gestor\CarpetasDocumentoController::class, 'destroy'])
        ->name('documentos.carpetas.destroy');

    // Alias legacy: redirige a la vista unificada con la pestaña correspondiente.
    Route::get('/reportes', fn() => redirect()->route('gestor.documentacion-reportes', ['tab' => 'reportes']))
        ->name('reportes');

    // ============================================================
    // CAJA CHICA — Vales (acceso por flag puede_gestionar_caja_chica)
    // El controlador valida el permiso en cada acción.
    // ============================================================
    Route::prefix('caja-chica')->name('caja-chica.')->group(function () {
        // Endpoint AJAX para autocompletado del solicitante
        Route::get('/solicitantes/buscar',
            [\App\Http\Controllers\Gestor\CajaChicaSolicitantesController::class, 'buscar'])
            ->name('solicitantes.buscar');

        // CRUD de vales
        Route::get('/',                  [\App\Http\Controllers\Gestor\CajaChicaController::class, 'index'])->name('index');
        Route::get('/nuevo',             [\App\Http\Controllers\Gestor\CajaChicaController::class, 'create'])->name('create');
        Route::post('/',                 [\App\Http\Controllers\Gestor\CajaChicaController::class, 'store'])->name('store');
        Route::get('/{vale}',            [\App\Http\Controllers\Gestor\CajaChicaController::class, 'show'])->name('show');
        Route::put('/{vale}',            [\App\Http\Controllers\Gestor\CajaChicaController::class, 'update'])->name('update');

        // Acciones sensibles (todas requieren reauth + motivo + opcional evidencia)
        Route::post('/{vale}/autorizar', [\App\Http\Controllers\Gestor\CajaChicaController::class, 'autorizar'])->name('autorizar');
        Route::post('/{vale}/rechazar',  [\App\Http\Controllers\Gestor\CajaChicaController::class, 'rechazar'])->name('rechazar');
        Route::post('/{vale}/cancelar',  [\App\Http\Controllers\Gestor\CajaChicaController::class, 'cancelar'])->name('cancelar');
        Route::post('/{vale}/factura',   [\App\Http\Controllers\Gestor\CajaChicaController::class, 'subirFactura'])->name('factura');

        // Ticket PDF (comprobante institucional)
        Route::get('/{vale}/ticket',     [\App\Http\Controllers\Gestor\CajaChicaController::class, 'imprimir'])->name('ticket');
    });

    // Tienda Institucional — Productos (CRUD)
    Route::resource('productos', \App\Http\Controllers\Gestor\ProductosController::class);
    Route::delete('/productos/imagenes/{imagen}',
        [\App\Http\Controllers\Gestor\ProductosController::class, 'eliminarImagen'])
        ->name('productos.imagenes.destroy');

    // Gestion de stock / variantes
    Route::post('/productos/variantes/{variante}/ajustar',
        [\App\Http\Controllers\Gestor\ProductosController::class, 'ajustarStock'])
        ->name('productos.variantes.ajustar');
    Route::patch('/productos/variantes/{variante}/stock-minimo',
        [\App\Http\Controllers\Gestor\ProductosController::class, 'actualizarStockMinimo'])
        ->name('productos.variantes.stock-minimo');
    Route::post('/productos/{producto}/variantes',
        [\App\Http\Controllers\Gestor\ProductosController::class, 'agregarVariante'])
        ->name('productos.variantes.agregar');
    Route::delete('/productos/variantes/{variante}',
        [\App\Http\Controllers\Gestor\ProductosController::class, 'eliminarVariante'])
        ->name('productos.variantes.eliminar');
    Route::post('/productos/{producto}/variantes/guardar-cambios',
        [\App\Http\Controllers\Gestor\ProductosController::class, 'guardarCambiosBatch'])
        ->name('productos.variantes.guardar-cambios');
    Route::post('/productos/{producto}/imagenes',
        [\App\Http\Controllers\Gestor\ProductosController::class, 'agregarImagenes'])
        ->name('productos.imagenes.agregar');
    Route::post('/productos/{producto}/reactivar',
        [\App\Http\Controllers\Gestor\ProductosController::class, 'reactivar'])
        ->name('productos.reactivar');
    Route::delete('/productos/{producto}/eliminar-permanente',
        [\App\Http\Controllers\Gestor\ProductosController::class, 'eliminarPermanente'])
        ->name('productos.eliminar-permanente');

    // Tienda Institucional — Bandeja de Pedidos (Gestor)
    Route::get('/pedidos',                [\App\Http\Controllers\Gestor\PedidosController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{pedido}',       [\App\Http\Controllers\Gestor\PedidosController::class, 'show'])->name('pedidos.show');
    Route::post('/pedidos/{pedido}/aprobar',  [\App\Http\Controllers\Gestor\PedidosController::class, 'aprobar'])->name('pedidos.aprobar');
    Route::post('/pedidos/{pedido}/rechazar', [\App\Http\Controllers\Gestor\PedidosController::class, 'rechazar'])->name('pedidos.rechazar');
    Route::post('/pedidos/{pedido}/listo',    [\App\Http\Controllers\Gestor\PedidosController::class, 'listoRecoger'])->name('pedidos.listo');
    Route::post('/pedidos/{pedido}/entregar', [\App\Http\Controllers\Gestor\PedidosController::class, 'entregar'])->name('pedidos.entregar');
    Route::post('/pedidos/{pedido}/cancelar', [\App\Http\Controllers\Gestor\PedidosController::class, 'cancelar'])->name('pedidos.cancelar');

    // Chatbot
    Route::post('/chatbot', [\App\Http\Controllers\ChatbotController::class, 'responder'])->name('chatbot');
});

require __DIR__.'/auth.php';
