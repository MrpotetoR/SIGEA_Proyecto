# Arquitectura SOA de SIGEA

Este documento documenta las cuatro capas principales del modelo SOA de SIGEA, alineado con el diagrama que compartiste.

## Capa de Acceso

La Capa de Acceso es la puerta de entrada del sistema. Aquí conviven las interfaces de usuario y el gateway de API.

Componentes reales:

- `routes/web.php` → rutas de los portales Blade
- `routes/api.php` → rutas de la API REST `/api/v1/`
- Panel Alumno
- Panel Docente
- Panel Director de Carrera
- Panel Servicios Escolares
- Panel Administrador
- `app/Http/Controllers/Auth/*`
- `app/Http/Controllers/Api/*`

Responsabilidades:

- recibir solicitudes HTTP y REST
- exponer interfaces de usuario y endpoints
- aplicar autenticación inicial
- enrutar la petición hacia la capa de procesos

Ejemplos:

- `POST /api/v1/login`
- `POST /api/v1/logout`
- `GET /api/v1/me`
- `GET /api/v1/perfil`
- `GET /api/v1/alumnos`
- `GET /api/v1/kardex`
- `GET /api/v1/noticias`

## Capa de Procesos

La Capa de Procesos actúa como orquestador. Esta capa decide qué servicios ejecutar y en qué orden.

Componentes reales:

- `app/Http/Controllers/Alumno/*`
- `app/Http/Controllers/Docente/*`
- `app/Http/Controllers/Director/*`
- `app/Http/Controllers/Servicios/*`
- `app/Http/Controllers/Api/*`
- middleware de autenticación y permisos (`auth:sanctum`, roles)
- validadores de request y policies

Responsabilidades:

- autenticar y autorizar usuarios
- validar permisos por rol
- traducir la petición HTTP a llamadas a servicios
- coordinar múltiples servicios cuando un proceso lo requiere
- devolver respuesta adecuada (JSON para API o vistas Blade)

Procesos clave:

- autenticación y autorización
- gestión de sesión y tokens
- consulta de información académica
- inscripción y gestión de grupos
- captura de calificaciones
- registro de asistencia
- validación de horas ACUDE
- generación de PDF
- gestión de trámites
- envío de notificaciones

## Capa de Servicios

La Capa de Servicios contiene la lógica de negocio. Cada servicio encapsula un dominio funcional específico.

Ubicación real: `app/Services/`

Servicios principales:

- `KardexService`
    - obtiene historial académico completo
    - calcula promedio general
    - genera PDF de kardex

- `CalificacionService`
    - registra calificaciones
    - genera boletas y reportes de rendimiento
    - calcula estadísticas por grupo

- `AsistenciaService`
    - registra asistencia diaria
    - obtiene reportes de asistencia
    - genera listas PDF para grupos

- `GrupoService`
    - crea y administra grupos
    - asigna horarios y docentes
    - inscribe y retira alumnos

- `PDFService`
    - centraliza la generación de documentos en PDF
    - orquesta servicios auxiliares para constancias y reportes

- `NotificacionService`
    - envía notificaciones internas en la aplicación
    - notifica procesos de pago, trámites y cambios de estado

- `EstadisticasCarreraService`
    - calcula índices de aprobación
    - genera datos de dashboard para dirección

- `SemaforoAcademicoService`
    - calcula el semáforo académico de los alumnos

Servicios REST asociados:

- Autenticación: `/api/v1/login`, `/api/v1/logout`, `/api/v1/me`
- Perfil: `/api/v1/perfil`
- Notificaciones: `/api/v1/notificaciones`, `/api/v1/notificaciones/{id}/leida`, `/api/v1/notificaciones/marcar-todas`
- Noticias: `apiResource /api/v1/noticias`
- Alumnos: `/api/v1/alumnos`, `/api/v1/alumnos/{id}`
- Kardex: `/api/v1/kardex`, `/api/v1/kardex/pdf`

## Capa de Recursos

La Capa de Recursos almacena la información y conecta servicios externos.

Componentes reales:

- modelos Eloquent en `app/Models/`
- migraciones en `database/migrations/`
- almacenamiento en `storage/`
- configuración de archivos `config/filesystems.php`

Recursos internos:

- Tablas de datos:
    - `TB_ALUMNOS`
    - `TB_DOCENTES`
    - `TB_GRUPOS`
    - `TB_MATERIAS`
    - `TB_KARDEX`
    - `TB_TRAMITES`
    - `TB_ACUDE_HORAS`
    - `TB_NOTIFICACIONES`
    - `TB_NOTICIAS`
    - `TB_CONSTANCIAS`
- Documentos y reportes:
    - expedientes PDF
    - vouchers de pago
    - documentos oficiales
    - imágenes para noticias

Servicios externos:

- SMTP / correo para recuperación de contraseña y notificaciones
- `barryvdh/laravel-dompdf` para generación de PDF
- almacenamiento local / respaldo de archivos

## Flujo de petición en el sistema

1. El cliente envía una petición en la `Capa de Acceso`.
2. El router de Laravel la envía al controlador en la `Capa de Procesos`.
3. El controlador valida permisos y llama a servicios en la `Capa de Servicios`.
4. Los servicios leen o actualizan datos desde la `Capa de Recursos`.
5. El resultado regresa al controlador y se entrega al cliente.

## Mapeo del diagrama a SIGEA

- `Capa de Acceso` = `routes/*` + portales de usuario + `app/Http/Controllers/Auth` + `Api Controllers`
- `Capa de Procesos` = `app/Http/Controllers/Alumno`, `Docente`, `Director`, `Servicios`, `Api`
- `Capa de Servicios` = `app/Services/*`
- `Capa de Recursos` = `app/Models/*`, `database/migrations/*`, `storage/*`, servicios externos

## Por qué esta versión es coherente con tu diagrama

- Define claramente qué entra al sistema (`Capa de Acceso`).
- Explica quién toma las decisiones y orquesta la lógica (`Capa de Procesos`).
- Muestra dónde vive la lógica de negocio (`Capa de Servicios`).
- Describe dónde se guardan y obtienen los datos (`Capa de Recursos`).

Esta versión está pensada para que cualquier lector entienda inmediatamente cada capa y su función en SIGEA.
