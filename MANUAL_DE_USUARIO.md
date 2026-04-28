# Manual de Usuario — SIGEA
## Sistema de Gestión Educativa Académica

---

**Versión:** 1.0
**Fecha:** Abril 2026
**Plataforma:** Aplicación web (compatible con Chrome, Edge, Firefox y Safari)

---

## Índice

1. [Introducción](#1-introducción)
2. [Requisitos y acceso](#2-requisitos-y-acceso)
3. [Inicio de sesión](#3-inicio-de-sesión)
4. [Recuperación de contraseña](#4-recuperación-de-contraseña)
5. [Funciones comunes a todos los usuarios](#5-funciones-comunes-a-todos-los-usuarios)
6. [Panel Administrador](#6-panel-administrador)
7. [Panel Servicios Escolares](#7-panel-servicios-escolares)
8. [Panel Director de Carrera](#8-panel-director-de-carrera)
9. [Panel Docente](#9-panel-docente)
10. [Panel Alumno](#10-panel-alumno)
11. [Solución de problemas frecuentes](#11-solución-de-problemas-frecuentes)
12. [Glosario](#12-glosario)

---

# 1. Introducción

## 1.1 ¿Qué es SIGEA?

**SIGEA** (Sistema de Gestión Educativa Académica) es una plataforma web que centraliza la gestión académica y administrativa de una institución educativa. Permite la administración de alumnos, docentes, directores de carrera, materias, grupos, calificaciones, asistencia, pagos y servicios escolares en un único entorno.

## 1.2 ¿Quiénes lo usan?

El sistema cuenta con **cinco perfiles (roles)** de usuario, cada uno con su propio panel y permisos:

| Rol | Función principal |
|---|---|
| **Administrador** | Gestiona al personal de Servicios Escolares y la asignación de carreras. |
| **Servicios Escolares** | Administra alumnos, docentes, directores, materias, grupos, ciclos, inscripciones y constancias de las carreras que tiene asignadas. |
| **Director de Carrera** | Supervisa los grupos, horarios, docentes, alumnos y métricas académicas de su carrera. |
| **Docente** | Imparte clases, registra asistencia, captura calificaciones y consulta sus grupos asignados. |
| **Alumno** | Consulta su horario, calificaciones, kárdex, pagos y noticias institucionales. |

## 1.3 Características destacadas

- ✅ **Acceso seguro** con autenticación por correo y contraseña.
- ✅ **Recuperación de contraseña** mediante código de 6 dígitos enviado al correo.
- ✅ **Notificaciones en tiempo real** dentro del sistema.
- ✅ **Modo oscuro** disponible en todos los paneles.
- ✅ **Diseño adaptable** a celular, tableta y computadora.
- ✅ **Asistente conversacional (chatbot)** integrado para resolver dudas frecuentes.
- ✅ **Generación de documentos PDF** (kárdex, constancias, reportes).

---

# 2. Requisitos y acceso

## 2.1 Requisitos técnicos

| Requisito | Mínimo | Recomendado |
|---|---|---|
| Navegador | Chrome 90+, Edge 90+, Firefox 88+, Safari 14+ | Última versión estable |
| Conexión a Internet | 1 Mbps | 5 Mbps o superior |
| Resolución de pantalla | 360 × 640 px (móvil) | 1280 × 720 px o superior |
| JavaScript | Habilitado | Habilitado |

## 2.2 URL de acceso

Para ingresar al sistema, abre tu navegador y escribe la dirección que te proporcionó tu institución (por ejemplo: `https://sigea.tudominio.edu.mx`).

> 📷 *Captura: pantalla de inicio del navegador mostrando la URL de SIGEA.*

---

# 3. Inicio de sesión

## 3.1 Pantalla de inicio de sesión

Al ingresar a la URL del sistema serás redirigido automáticamente a la pantalla de **inicio de sesión**.

> 📷 *Captura: pantalla de login de SIGEA con el panel azul a la izquierda y formulario blanco a la derecha.*

La pantalla está dividida en dos secciones:

- **Lado izquierdo:** banner institucional con animación de fondo y el lema *"Sistema de Gestión Académica"*.
- **Lado derecho:** formulario de inicio de sesión.

## 3.2 Pasos para iniciar sesión

1. En el campo **Correo electrónico**, escribe tu correo registrado.
2. En el campo **Contraseña**, escribe tu contraseña.
3. (Opcional) Marca la casilla **Recordarme** si deseas mantener tu sesión iniciada en este equipo.
4. Haz clic en el botón **Iniciar sesión**.

> 📷 *Captura: formulario de inicio de sesión con los campos correo y contraseña, y el botón "Iniciar sesión".*

## 3.3 Redirección automática

Una vez iniciada la sesión, el sistema te enviará automáticamente al panel correspondiente a tu rol:

| Rol | Panel destino |
|---|---|
| Administrador | `/admin/dashboard` |
| Servicios Escolares | `/servicios/dashboard` |
| Director de Carrera | `/director/dashboard` |
| Docente | `/docente/dashboard` |
| Alumno | `/alumno/dashboard` |

## 3.4 Mensajes de error

Si las credenciales son incorrectas, verás un mensaje de error en rojo en la parte superior del formulario:

> *"Las credenciales proporcionadas no coinciden con nuestros registros."*

> 📷 *Captura: pantalla de login mostrando el mensaje de error en rojo.*

Verifica:
- Que el correo esté escrito correctamente (sin espacios al inicio o al final).
- Que la contraseña use mayúsculas y minúsculas como corresponde.
- Que tu cuenta esté activa (consulta con tu administrador si tienes dudas).

---

# 4. Recuperación de contraseña

Si olvidas tu contraseña, SIGEA cuenta con un sistema de recuperación de **3 pasos** mediante un código de 6 dígitos enviado a tu correo electrónico.

## 4.1 Paso 1 — Solicitar el código

1. En la pantalla de inicio de sesión, haz clic en el enlace **"¿Olvidaste tu contraseña?"** ubicado bajo el formulario.
2. Se abrirá la pantalla **"Recuperar contraseña"**.
3. Escribe tu correo electrónico registrado.
4. Haz clic en **Enviar código**.

> 📷 *Captura: pantalla "Recuperar contraseña" con el campo de correo y botón "Enviar código".*

## 4.2 Paso 2 — Ingresar el código

1. Revisa la bandeja de entrada de tu correo (también la carpeta de **spam** o **correo no deseado**).
2. Encontrarás un correo de SIGEA con un **código de 6 dígitos**. El código tiene una vigencia de **15 minutos**.
3. Vuelve al sistema y escribe el código en los seis recuadros.
4. Haz clic en **Verificar código**.

> 📷 *Captura: correo electrónico recibido con el código de 6 dígitos resaltado.*

> 📷 *Captura: pantalla "Código de verificación" con los 6 recuadros para escribir el código.*

**Si el código expiró o no llegó:** haz clic en el enlace **"Reenviar código"** en la parte inferior. Puedes solicitar un nuevo envío hasta 3 veces por minuto.

## 4.3 Paso 3 — Crear la nueva contraseña

1. Una vez verificado el código, se abrirá la pantalla **"Nueva contraseña"**.
2. Escribe tu nueva contraseña (mínimo 8 caracteres).
3. Confírmala en el campo siguiente.
4. Haz clic en **Cambiar contraseña**.

> 📷 *Captura: pantalla "Nueva contraseña" con los dos campos de contraseña.*

Al finalizar, podrás iniciar sesión con tus nuevas credenciales.

---

# 5. Funciones comunes a todos los usuarios

Las siguientes funciones están disponibles en todos los paneles, independientemente de tu rol.

## 5.1 Barra superior

> 📷 *Captura: barra superior del panel mostrando los iconos de tema, notificaciones, chatbot y búsqueda.*

| Icono | Función |
|---|---|
| 🌙 / ☀️ | **Tema claro / oscuro** — alterna el modo visual del sistema. |
| 🔔 | **Notificaciones** — abre el panel de notificaciones recientes. El número rojo indica cuántas no has leído. |
| 💬 | **Chatbot** — abre el asistente conversacional de SIGEA. |
| 🔍 | **Búsqueda rápida** — busca alumnos, docentes o grupos del sistema. |

## 5.2 Cambiar entre modo claro y oscuro

Haz clic en el icono de **luna 🌙** (modo claro activo) o **sol ☀️** (modo oscuro activo) ubicado en la barra superior. La preferencia se guarda automáticamente en tu navegador.

> 📷 *Captura: comparativa lado a lado del mismo panel en modo claro y modo oscuro.*

## 5.3 Sidebar (menú lateral)

Cada panel cuenta con un **menú lateral izquierdo** que muestra las opciones disponibles para tu rol, agrupadas por categorías. La opción activa se resalta en color azul.

En la parte inferior del sidebar siempre aparece:
- 🔑 **Cambiar contraseña**
- 👤 **Tu nombre y rol**
- 🚪 **Cerrar sesión** (icono de salida)

> 📷 *Captura: sidebar del panel con la sección activa resaltada y la sección inferior con el usuario.*

## 5.4 Notificaciones

Las notificaciones se muestran automáticamente cuando ocurre algún evento relevante para ti, como:
- Una calificación registrada (alumno).
- Un váucher de pago aprobado o rechazado (alumno).
- Una nueva carrera sin personal asignado (administrador).
- Una nueva noticia institucional (todos los roles).

### Cómo consultarlas

1. Haz clic en el icono 🔔 de la barra superior.
2. Se abrirá un panel lateral con las notificaciones más recientes.
3. Las **no leídas** aparecen resaltadas con un fondo azul claro.

> 📷 *Captura: panel de notificaciones desplegado mostrando 2-3 notificaciones de ejemplo.*

### Marcar como leídas

- Haz clic sobre una notificación para marcarla como leída e ir a la pantalla relacionada.
- Haz clic en **"Marcar todas como leídas"** para limpiar el contador.

## 5.5 Chatbot SIGEA

El asistente conversacional puede responder preguntas frecuentes según tu rol.

### Cómo usarlo

1. Haz clic en el icono 💬 de la barra superior.
2. Se abrirá una ventana flotante de chat.
3. Escribe tu pregunta en lenguaje natural (por ejemplo: *"¿Cuándo es la próxima fecha de pago?"* o *"¿Cómo subo mi váucher?"*).
4. El asistente responderá con información contextual a tu rol.

> 📷 *Captura: ventana del chatbot abierta con una pregunta del usuario y la respuesta del asistente.*

## 5.6 Cambiar contraseña

1. En el menú lateral, haz clic en **"Cambiar contraseña"**.
2. Escribe tu **contraseña actual**.
3. Escribe tu **nueva contraseña** (mínimo 8 caracteres).
4. Confírmala en el campo **"Confirmar contraseña"**.
5. Haz clic en **Actualizar**.

> 📷 *Captura: formulario "Cambiar contraseña" con los tres campos llenos y el botón Actualizar.*

## 5.7 Cerrar sesión

Haz clic en el icono de salida 🚪 ubicado junto a tu nombre en la parte inferior del sidebar. Serás redirigido a la pantalla de inicio de sesión.

---

# 6. Panel Administrador

> 🔑 **Acceso de prueba:** `admin@sigea.edu.mx` / `admin2026`

El **panel Administrador** es el panel de mayor nivel del sistema. Permite gestionar al **Personal de Servicios Escolares**, **asignar carreras** a cada miembro del personal y **crear nuevos administradores**.

## 6.1 Dashboard (Overview)

Al iniciar sesión como administrador, accederás al **Dashboard** que muestra un resumen general del estado del sistema:

> 📷 *Captura: dashboard del administrador mostrando los 4 KPIs y la lista de personal reciente.*

### Indicadores principales (KPIs)

| Indicador | Descripción |
|---|---|
| **Personal S.E.** | Total de personal de Servicios Escolares activo. |
| **Administradores** | Total de cuentas con rol administrador. |
| **Carreras asignadas** | Cuántas carreras tienen un personal a cargo (de cuántas en total). |
| **Sin asignar** | Carreras que aún no tienen personal de SE asignado. |

### Alerta de carreras sin asignar

Si hay carreras sin personal asignado, aparecerá un **recuadro ámbar** con la lista de esas carreras y un enlace directo a *"Ir a asignar carreras"*.

> 📷 *Captura: alerta ámbar mostrando las carreras pendientes de asignar.*

### Personal reciente

En la parte inferior se muestra una tabla con el personal de SE registrado recientemente, junto al número de carreras que administra.

## 6.2 Personal de Servicios Escolares

### 6.2.1 Listar personal

1. En el sidebar, haz clic en **"Personal S.E."** (sección **Personal**).
2. Verás la lista completa del personal registrado, con columnas:
   - **Nombre completo**
   - **Correo**
   - **Especialidad**
   - **Carreras asignadas** (etiquetas con la clave de cada carrera)
   - **Acciones** (Ver / Editar / Eliminar)
3. Usa el campo **Buscar** para filtrar por nombre, apellido o correo.

> 📷 *Captura: lista de Personal S.E. con los registros visibles y el filtro de búsqueda.*

### 6.2.2 Registrar nuevo personal

1. En la parte superior derecha, haz clic en el botón verde **"+ Nuevo personal"**.
2. Se abrirá un formulario con dos secciones lado a lado:

#### Sección 1 — Datos del personal

| Campo | Obligatorio | Descripción |
|---|:---:|---|
| Nombre(s) | ✅ | Solo letras y espacios, máximo 80 caracteres. |
| Apellidos | ✅ | Solo letras y espacios, máximo 100 caracteres. |
| Correo electrónico | ✅ | Debe ser único en el sistema. La contraseña inicial se generará automáticamente como `servicios2026`. |
| Cédula profesional | ❌ | Hasta 30 caracteres. |
| RFC | ❌ | Se convierte automáticamente a mayúsculas. |
| Especialidad | ✅ | Área de especialización (ej. *"Administración educativa"*). |
| Asignar carreras | ❌ | Selecciona hasta **4 carreras** mediante casillas de verificación. Solo aparecen las carreras disponibles (sin asignar a otro personal). |

#### Sección 2 — Documentación (8 PDFs obligatorios)

Todos estos documentos son **obligatorios** y deben subirse en formato **PDF** (máximo 5 MB cada uno):

1. Comprobante de domicilio
2. INE (Identificación oficial)
3. Número de seguridad social
4. Carta de motivos
5. CURP
6. Acta de nacimiento
7. Constancia de situación fiscal
8. Cédula profesional

> 📷 *Captura: formulario de "Nuevo personal" mostrando ambas secciones (datos a la izquierda, documentos a la derecha).*

3. Haz clic en **Registrar personal**.
4. El sistema validará todos los campos y archivos. Si todo es correcto, regresarás al listado con un mensaje verde de confirmación.

### 6.2.3 Ver detalles del personal

1. En el listado, haz clic en **"Ver"** en la columna **Acciones**.
2. Se mostrará una pantalla con:
   - Datos generales (nombre, correo, especialidad, cédula, RFC).
   - Carreras asignadas (etiquetas azules).
   - Documentación cargada (con enlace a cada PDF).

> 📷 *Captura: pantalla de detalle del personal con sus datos, carreras y documentos.*

### 6.2.4 Editar personal

1. En el listado o desde el detalle, haz clic en **"Editar"**.
2. Modifica los campos que necesites. Los documentos solo se reemplazan si subes un archivo nuevo.
3. Haz clic en **Guardar cambios**.

> ⚠️ **Importante:** las carreras asignadas no se modifican desde aquí — debes ir a **"Asignación de carreras"** (sección 6.3).

### 6.2.5 Eliminar personal

1. En el listado, haz clic en **"Eliminar"** en la columna Acciones.
2. Confirma la acción en el cuadro de diálogo.
3. El personal queda inactivo (eliminación lógica) y **sus carreras quedan automáticamente disponibles** para reasignar.

## 6.3 Asignación de carreras

Esta sección permite gestionar qué carreras administra cada miembro del personal de SE.

### Reglas de asignación

- ✅ Cada personal de SE puede administrar **hasta 4 carreras**.
- ✅ Cada carrera puede ser administrada **por un único personal** (no se permite asignación duplicada).
- ✅ Una carrera **debe** tener un personal asignado para que aparezca en el panel de Servicios Escolares.

### 6.3.1 Asignar una carrera

1. En el sidebar, haz clic en **"Asignación de carreras"**.
2. Verás tres secciones:
   - **Alerta ámbar** con las carreras sin asignar (si las hay).
   - **Formulario de nueva asignación**.
   - **Tabla con asignaciones actuales**.
3. En el formulario:
   - Selecciona el **Personal de SE**.
   - Selecciona la **Carrera**.
   - Haz clic en **Asignar**.

> 📷 *Captura: pantalla de asignaciones con el formulario y la tabla de asignaciones existentes.*

### 6.3.2 Desasignar una carrera

En la tabla de asignaciones, haz clic sobre la **etiqueta azul de la carrera** (al pasar el cursor cambia a roja con una "×"). Confirma la acción.

> ⚠️ La carrera quedará **sin asignar** y deberás reasignarla a otro personal para que vuelva a estar visible en el panel de Servicios Escolares.

## 6.4 Administradores

Permite registrar otros usuarios con rol administrador.

### 6.4.1 Listar administradores

En el sidebar, haz clic en **"Admins"** (sección **Administradores**). Verás la lista con nombre, correo, estado, fecha de registro y acciones.

> 📷 *Captura: lista de administradores con tu cuenta marcada con la etiqueta "Tú".*

### 6.4.2 Registrar nuevo administrador

1. Haz clic en **"+ Nuevo admin"**.
2. Llena los campos:
   - **Nombre completo** (solo letras y espacios)
   - **Correo electrónico** (único)
   - **Contraseña** (mínimo 8 caracteres)
   - **Confirmar contraseña**
3. Haz clic en **Crear administrador**.

### 6.4.3 Reglas de seguridad

- 🚫 No puedes **eliminarte a ti mismo**.
- 🚫 No se puede eliminar al **último administrador activo** del sistema.
- 🔑 Solo otros administradores pueden gestionar cuentas de admin.

## 6.5 Notificaciones del administrador

Cuando se crea una **nueva carrera** en el sistema, todos los administradores activos reciben automáticamente una notificación con el mensaje:

> *"Se creó la carrera "X" y aún no tiene personal de Servicios Escolares asignado."*

Al hacer clic en la notificación, serás llevado directamente a la pantalla de Asignación de carreras.

> 📷 *Captura: notificación de "Nueva carrera sin personal asignado" en el panel de notificaciones.*

---

# 7. Panel Servicios Escolares

> 🔑 **Acceso de prueba:** `servicios@sigea.edu.mx` / `password`

El panel de **Servicios Escolares** es el centro operativo del día a día académico. Aquí se gestionan alumnos, docentes, directores de carrera, materias, grupos, ciclos escolares, inscripciones, constancias, noticias y reportes.

> ⚠️ **Importante:** las acciones que realices solo afectarán a las **carreras que el administrador te haya asignado**. Si no tienes carreras asignadas, no verás datos en ninguna sección. En ese caso, contacta al administrador.

## 7.1 Dashboard

Al iniciar sesión, verás un resumen con:

- **Total de alumnos activos**
- **Bajas temporales**
- **Total de docentes**
- **Total de carreras** (de las que tienes asignadas)
- **Ciclo escolar activo**

> 📷 *Captura: dashboard de Servicios Escolares con los KPIs.*

## 7.2 Alumnos

### 7.2.1 Listar alumnos

1. En el sidebar, haz clic en **"Alumnos"** (sección **Alumnos**).
2. Verás la lista con: matrícula, nombre, carrera, cuatrimestre, estado de pago, estatus y acciones.
3. Usa los filtros superiores para acotar:
   - **Buscar** (por nombre, apellido o matrícula)
   - **Carrera** (solo aparecen las que tienes asignadas)
   - **Estatus** (Activo / Baja temporal / Baja definitiva)
   - **Estado de pago** (Pagado / En revisión / Sin pago)

> 📷 *Captura: lista de alumnos con filtros y la tabla de resultados.*

### 7.2.2 Registrar nuevo alumno

1. Haz clic en **"+ Nuevo alumno"**.
2. El formulario tiene **cuatro secciones**:

#### a) Datos del alumno
- Nombre, Apellidos, Correo electrónico, Carrera, Cuatrimestre actual, Tutor (opcional).

#### b) Datos del padre/tutor
- Nombre, Apellidos, Correo, Teléfono, Teléfono de emergencia, INE (PDF).

#### c) Pagos por cuatrimestre
- Sube los vouchers PDF de los cuatrimestres ya cubiertos. **Deben ir en orden consecutivo** desde el 1°.

#### d) Documentación del alumno
- 8 documentos PDF obligatorios (Acta, CURP, Comprobante de domicilio, etc.).

> 📷 *Captura: formulario de "Nuevo alumno" con las 4 secciones visibles.*

3. Haz clic en **Registrar alumno**. La matrícula se genera automáticamente con el formato `[CLAVE-CARRERA][AÑO][NÚMERO]` (ej. `DSM20260005`).

### 7.2.3 Ver y editar alumno

1. En el listado, haz clic en **"Ver"** para abrir la ficha completa.
2. Desde ahí puedes:
   - Editar datos generales (botón **Editar**).
   - **Aprobar o rechazar** vouchers de pago pendientes.
   - **Registrar baja** (temporal o definitiva) con motivo y fecha.
   - **Registrar reingreso** si estaba en baja temporal.
   - Eliminar documentos individuales para volver a cargarlos.

> 📷 *Captura: ficha de detalle del alumno con sus datos, pagos, documentos y botones de acción.*

### 7.2.4 Aprobar o rechazar pagos (vouchers)

Cuando un alumno sube un voucher desde su panel, llega a Servicios Escolares como **"En revisión"**.

1. Abre la ficha del alumno (o ve a la sección de pagos).
2. En el voucher pendiente, haz clic en **"Aprobar"** o **"Rechazar"**.
3. Si rechazas, escribe un comentario explicando qué corregir. El alumno recibirá una notificación con tu comentario.

> 📷 *Captura: tarjeta de voucher pendiente con los botones Aprobar/Rechazar.*

## 7.3 Inscripciones

Permite asignar alumnos a grupos del ciclo activo.

1. En el sidebar, haz clic en **"Inscripciones"**.
2. En el panel izquierdo, llena el formulario **"Nueva inscripción"**:
   - Carrera (filtra los grupos)
   - Ciclo escolar
   - Alumno (busca por nombre o matrícula)
   - Grupo
3. Haz clic en **Inscribir alumno**.
4. En el panel derecho verás la lista de inscripciones existentes con filtros y opción de eliminar.

> 📷 *Captura: pantalla de inscripciones con el formulario a la izquierda y la lista a la derecha.*

## 7.4 Constancias

Genera constancias oficiales en PDF.

1. En el sidebar, haz clic en **"Constancias"**.
2. Llena el formulario:
   - **Alumno** (busca por matrícula o nombre)
   - **Tipo de constancia** (Estudios / Calificaciones / Buena conducta / etc.)
3. Haz clic en **Generar constancia**.
4. El PDF se descarga automáticamente y queda registrado en el historial del alumno.

> 📷 *Captura: pantalla de generación de constancia con el alumno seleccionado.*

> 📷 *Captura: ejemplo de PDF generado de una constancia de estudios.*

## 7.5 Docentes

### 7.5.1 Listar docentes

En la sección **Académico → Docentes**. Filtra por nombre, apellido o especialidad.

> 📷 *Captura: lista de docentes con filtros y la tabla.*

### 7.5.2 Registrar docente

1. Clic en **"+ Nuevo docente"**.
2. Llena los datos personales (nombre, apellidos, correo, cédula, RFC, especialidad).
3. Indica si es **tutor**, las **horas contratadas** y **carreras donde imparte**.
4. Sube los **8 PDFs obligatorios** (mismos que para directores).
5. Clic en **Registrar docente**.

## 7.6 Directores de carrera

### 7.6.1 Listar y filtrar

Ve a **Académico → Directores**. Verás la lista con la carrera dirigida por cada uno.

### 7.6.2 Registrar director

1. Clic en **"+ Nuevo director"**.
2. Llena los datos personales y selecciona la carrera a dirigir (las que tienes asignadas).
3. Sube los **8 PDFs obligatorios**.
4. Clic en **Registrar director**.

> 📷 *Captura: formulario de nuevo director con todos los campos.*

## 7.7 Carreras

Gestiona las carreras académicas de tu institución.

### 7.7.1 Crear carrera

1. Ve a **Académico → Carreras**.
2. Clic en **"+ Nueva carrera"**.
3. Llena:
   - **Nombre de la carrera**
   - **Clave** (se autogenera, puedes editarla)
   - **Área académica** (Ciencias de la salud, Ingenierías, etc.)
   - **Tipo de periodo** (Cuatrimestre o Semestre — ⚠️ **inmutable** una vez creada)
   - **Director** (opcional)
4. Clic en **Crear**.

> 📷 *Captura: formulario de nueva carrera.*

> 📌 **Nota:** Al crear una nueva carrera, todos los administradores reciben una notificación para que asignen el personal correspondiente.

## 7.8 Materias

Gestiona el plan de estudios de cada carrera.

1. Ve a **Académico → Materias**.
2. Filtra por carrera.
3. Para crear: clic en **"+ Nueva materia"** y llena:
   - **Nombre de la materia**
   - **Carrera**
   - **Cuatrimestre** (en qué cuatrimestre se imparte)
   - **Horas por semana**

## 7.9 Ciclos escolares

Gestiona los periodos académicos.

1. Ve a **Académico → Ciclos Escolares**.
2. Para crear: **"+ Nuevo ciclo"**, llena nombre (`2026-2`), fecha de inicio y fecha de fin.

## 7.10 Noticias

Publica avisos institucionales que verán los demás usuarios.

1. Ve a **Contenido → Noticias**.
2. Clic en **"+ Nueva noticia"**.
3. Llena el formulario:
   - **Título**
   - **Contenido** (con editor de texto enriquecido)
   - **Imagen de portada** (opcional)
   - **Audiencia** (Todos / Alumnos / Docentes / Directores / etc.)
   - **Fecha de publicación** (ahora o programada)
4. Clic en **Publicar**.

> 📷 *Captura: editor de noticia con todos los campos.*

Al publicar, los usuarios destinatarios reciben una notificación inmediata.

## 7.11 Documentos institucionales

Archivos descargables (reglamentos, formatos, calendarios) que pueden consultar los demás usuarios.

1. Ve a **Contenido → Documentos**.
2. Clic en **"+ Subir documento"**.
3. Selecciona el archivo, ponle un título y categoría.

## 7.12 Reportes

Reportes globales de la institución (alumnos por carrera, índices de aprobación, asistencia general, etc.).

1. Ve a **Contenido → Reportes**.
2. Selecciona el tipo de reporte y los filtros.
3. Visualiza en pantalla o descarga en PDF.

> 📷 *Captura: pantalla de reportes con los gráficos generados.*

---

# 8. Panel Director de Carrera

> 🔑 **Acceso de prueba:** `director@sigea.edu.mx` / `password`

El panel de **Director de Carrera** está enfocado en la **supervisión académica** de la(s) carrera(s) que diriges. Tienes acceso de **lectura/análisis** sobre la mayoría de los datos, y **gestión** sobre grupos y horarios.

## 8.1 Dashboard

Muestra un resumen de tu carrera:

- Total de alumnos
- Total de docentes
- Total de grupos activos
- Índice de aprobación general del ciclo
- Acceso rápido a las secciones más usadas

> 📷 *Captura: dashboard del director con tarjetas de KPIs.*

## 8.2 Perfil

Tu información personal (nombre, correo, especialidad, cédula, RFC).

## 8.3 Grupos

### 8.3.1 Listar grupos

En el sidebar, **Grupos**. Verás todos los grupos de tu carrera con su clave, ciclo, tutor, número de alumnos.

### 8.3.2 Crear grupo

1. Clic en **"+ Nuevo grupo"**.
2. Llena: **Cuatrimestre**, **Ciclo escolar**, **Tutor (docente)**.
3. La clave del grupo se genera automáticamente.
4. Clic en **Crear**.

> 📷 *Captura: formulario de nuevo grupo.*

### 8.3.3 Ver detalles del grupo

Al entrar en un grupo verás:
- Datos del grupo
- Lista de alumnos inscritos
- Botones para **inscribir** o **desinscribir** alumnos
- Horario asignado

> 📷 *Captura: vista de detalle de un grupo.*

## 8.4 Horarios

Gestiona los horarios de clase asignando docente, materia, día y hora a cada grupo.

1. Ve a **Horarios**.
2. Clic en **"+ Nuevo horario"**.
3. Llena: **Grupo**, **Materia**, **Docente**, **Día de la semana**, **Hora de inicio**, **Hora de fin**.
4. Clic en **Crear**.

> 📷 *Captura: formulario de nuevo horario.*

## 8.5 Docentes

Solo lectura. Lista de docentes que imparten en tu carrera con su especialidad y carga de grupos.

## 8.6 Alumnos

Solo lectura. Lista completa de alumnos de tu carrera.

### 8.6.1 Historial académico de un alumno

Haz clic en cualquier alumno para ver su **historial académico completo**: calificaciones por cuatrimestre, materias aprobadas/reprobadas, promedio, semáforo académico.

> 📷 *Captura: historial académico de un alumno con tabla de calificaciones.*

## 8.7 Asistencia

Consulta la asistencia general de cualquier grupo en un rango de fechas.

1. Ve a **Asistencia**.
2. Selecciona el **Grupo** y opcionalmente una **fecha**.
3. Clic en **Consultar**.
4. Verás la tabla con presentes, ausentes, retardos y % de asistencia por alumno.

> 📷 *Captura: pantalla de asistencia con la tabla de resultados.*

## 8.8 Índice de aprobación

Estadísticas de aprobación y reprobación por carrera y ciclo.

> 📷 *Captura: pantalla de índices con barras de aprobación y reprobación.*

## 8.9 Evaluación docente

Resultados de las evaluaciones que los alumnos hicieron a los docentes de tu carrera.

## 8.10 Plan de estudios

Vista del plan curricular completo: materias por cuatrimestre con sus horas semanales.

## 8.11 Noticias

Lectura de las noticias institucionales dirigidas a los directores.

---

# 9. Panel Docente

> 🔑 **Acceso de prueba:** `docente@sigea.edu.mx` / `password`

El panel **Docente** te permite gestionar tus grupos asignados, registrar asistencia, capturar calificaciones, generar reportes y registrar actividades complementarias.

## 9.1 Dashboard

Resumen de:
- Tus grupos activos del ciclo
- Próximas clases del día (según tu horario)
- Resumen de calificaciones pendientes de capturar
- Promedio de tu evaluación docente

> 📷 *Captura: dashboard del docente.*

## 9.2 Perfil

Datos personales (nombre, correo, especialidad, cédula, RFC, horas contratadas).

## 9.3 Grupos asignados

Lista de los grupos donde impartes clase. Al hacer clic en uno, ves la lista de alumnos y horario.

## 9.4 Mi horario

Vista semanal tipo calendario con tus clases. Cada bloque muestra la materia, grupo, salón (si aplica) y hora.

> 📷 *Captura: horario semanal del docente con bloques de clase.*

## 9.5 Tutorados (si aplica)

Si eres tutor, verás la lista de alumnos a tu cargo y podrás consultar su rendimiento y semáforo académico.

## 9.6 Asistencia

### 9.6.1 Pasar asistencia del día

1. Ve a **Asistencia**.
2. Selecciona el grupo donde tienes clase ese día.
3. Aparecerá la lista de alumnos con tres opciones por cada uno:
   - ✅ Presente
   - ⏰ Retardo
   - ❌ Ausente
4. Marca el estatus de cada alumno.
5. Haz clic en **Guardar asistencia**.

> 📷 *Captura: pantalla "Pasar asistencia hoy" con la lista de alumnos.*

### 9.6.2 Ver historial de asistencia

1. En la pantalla de asistencia de un grupo, haz clic en **"Historial"**.
2. Selecciona el rango de fechas (puedes usar los accesos rápidos *Semana* o *Mes*).
3. Verás una **matriz tipo Excel** con:
   - Columnas: cada día con clase
   - Filas: cada alumno
   - Celdas: ✓ presente, ✗ ausente, ⏰ retardo
   - Columna de totales: % de asistencia, total de retardos y faltas
4. **Haz clic en cualquier celda** para editar el estatus de ese día puntual.

> 📷 *Captura: historial de asistencia con la matriz completa.*

> 💡 **Tip:** Los alumnos con 3 o más faltas aparecen resaltados en rojo (alerta).

## 9.7 Calificaciones

### 9.7.1 Capturar calificaciones

1. Ve a **Calificaciones**.
2. Selecciona el grupo.
3. Aparecerá la lista de alumnos con tres columnas: **Parcial 1**, **Parcial 2**, **Parcial 3**.
4. Captura las calificaciones (de 0 a 10).
5. Haz clic en **Guardar**.

> 📷 *Captura: pantalla de captura de calificaciones con la tabla.*

> 💡 **Calificación aprobatoria:** ≥ 7. Las celdas con calificación reprobatoria aparecen en rojo.

### 9.7.2 Editar calificación previa

Haz clic sobre la calificación, modifícala y haz clic en **Guardar** otra vez.

## 9.8 Reportes

### 9.8.1 Reporte de asistencia

Selecciona grupo y rango de fechas. Genera un PDF con la tabla completa de asistencia.

### 9.8.2 Reporte de rendimiento

Selecciona grupo y ciclo. Genera un PDF con el promedio y porcentaje de aprobación de cada alumno.

> 📷 *Captura: PDF de reporte de rendimiento generado.*

## 9.9 Horas culturales/deportivas

Registra las horas que los alumnos dedicaron a actividades culturales o deportivas.

## 9.10 Servicio social

Registra y supervisa el servicio social de los alumnos a tu cargo.

## 9.11 Resultados de evaluación docente

Consulta los resultados de tu propia evaluación: promedio, comentarios de los alumnos.

## 9.12 Noticias

Lectura de las noticias institucionales dirigidas a docentes.

---

# 10. Panel Alumno

> 🔑 **Acceso de prueba:** `alumno@sigea.edu.mx` / `password`

El panel **Alumno** te permite consultar tu información académica y administrativa, así como subir tus vouchers de pago y participar en evaluaciones.

## 10.1 Dashboard

Resumen personalizado:
- Tu próxima clase
- Tu promedio general
- Estado de pago del cuatrimestre actual
- Noticias relevantes
- Notificaciones

> 📷 *Captura: dashboard del alumno con su resumen.*

## 10.2 Mi perfil

Tus datos personales y los de tu padre/tutor.

> 📷 *Captura: ficha de perfil del alumno.*

## 10.3 Mi horario

Vista semanal tipo calendario con tus clases.

> 📷 *Captura: horario del alumno.*

## 10.4 Mis calificaciones

Tabla con tus calificaciones por materia y parcial. Las **reprobatorias (< 7)** aparecen en rojo.

> 📷 *Captura: pantalla de calificaciones del alumno.*

## 10.5 Kárdex

Tu historial académico oficial completo.

1. Ve a **Kárdex**.
2. Verás la tabla con todas tus materias acumuladas, calificación final y estatus (Aprobada / Reprobada / En curso).
3. Haz clic en **"Descargar PDF"** para obtener tu kárdex oficial.

> 📷 *Captura: pantalla de kárdex con el botón de descarga.*

> 📷 *Captura: PDF de kárdex generado.*

## 10.6 Historial académico

Vista cuatrimestre por cuatrimestre con detalle de cada periodo.

## 10.7 Horas culturales/deportivas

Consulta las horas que has acumulado por participar en actividades extracurriculares.

## 10.8 Servicio social

Estado de tu servicio social: institución, horas acumuladas, fecha estimada de término.

## 10.9 Evaluación docente

Una vez por ciclo se habilita la **encuesta de evaluación docente**.

1. Ve a **Evaluación docente**.
2. Verás la lista de tus docentes pendientes de evaluar.
3. Haz clic en uno y responde el cuestionario.
4. Al terminar, haz clic en **Enviar evaluación**.

> 📷 *Captura: cuestionario de evaluación docente.*

> ℹ️ **Importante:** las respuestas son **anónimas** — el docente solo verá el promedio y los comentarios consolidados.

## 10.10 Mis docentes

Lista de los docentes que te imparten clase con su especialidad y forma de contacto.

## 10.11 Mis pagos

Aquí subes los **vouchers de pago** por cuatrimestre.

### 10.11.1 Subir voucher

1. Ve a **Mis Pagos**.
2. En la fila del cuatrimestre actual ("Disponible para carga"):
   - Haz clic en **"Seleccionar archivo"** y elige el PDF del voucher.
   - Haz clic en **+ Subir**.
3. El voucher pasa a estado **"Pendiente de revisión"** (color ámbar).
4. Servicios Escolares lo revisará en breve. Recibirás una notificación cuando sea aprobado o rechazado.

> 📷 *Captura: pantalla "Mis pagos" con un voucher pendiente y otro aprobado.*

### 10.11.2 Estados del voucher

| Color | Estado | Significado |
|---|---|---|
| 🟢 Verde | Aprobado | Tu pago fue validado. |
| 🟡 Ámbar | Pendiente de revisión | Tu voucher está siendo revisado. |
| 🔴 Rojo | Rechazado | Hay un problema. Revisa el comentario y vuelve a subir el voucher corregido. |
| 🔒 Gris (candado) | Bloqueado | Debes completar el cuatrimestre anterior antes de pagar éste. |

### 10.11.3 Voucher rechazado

Si tu voucher fue rechazado:
1. Lee el motivo en el recuadro rojo bajo el cuatrimestre.
2. Corrige tu comprobante.
3. Haz clic en **"Resubir"** para subir el nuevo PDF.

> 📷 *Captura: voucher rechazado con el motivo y el botón Resubir.*

## 10.12 Noticias

Lee las noticias institucionales dirigidas a alumnos.

> 📷 *Captura: lista de noticias del alumno.*

---

# 11. Solución de problemas frecuentes

## 11.1 No puedo iniciar sesión

| Problema | Causa probable | Solución |
|---|---|---|
| "Credenciales incorrectas" | Correo o contraseña mal escritos. | Verifica mayúsculas/minúsculas y prueba copiar-pegar. |
| "Tu cuenta no está activa" | Tu cuenta fue desactivada. | Contacta al administrador o a Servicios Escolares. |
| No llega el correo de recuperación | Filtro de spam o correo escrito mal. | Revisa la carpeta de **Spam** y verifica el correo. |
| El código expiró | Pasaron más de 15 minutos. | Haz clic en **"Reenviar código"**. |

## 11.2 No veo ningún dato (Servicios Escolares)

Tu cuenta no tiene **carreras asignadas**. Contacta al administrador para que te asigne las carreras correspondientes.

## 11.3 No puedo subir un PDF

| Problema | Solución |
|---|---|
| El archivo pesa más de 5 MB | Comprime el PDF (puedes usar herramientas en línea gratuitas). |
| El archivo no es PDF | Solo se aceptan archivos `.pdf`. Convierte tu imagen o documento a PDF. |
| El navegador no muestra el botón | Recarga con `Ctrl + Shift + R`. |

## 11.4 No veo bien el sistema en mi celular

SIGEA es totalmente responsive. Si notas elementos cortados:
1. Asegúrate de tener el navegador actualizado.
2. Recarga la página con limpieza de caché (`Ctrl + Shift + R` en escritorio, "Recargar" en móvil).
3. Gira tu pantalla a horizontal para ver tablas anchas (asistencia, calificaciones).

## 11.5 No me llega una notificación importante

Las notificaciones aparecen en el icono 🔔 de la barra superior. Si crees que falta una:
1. Refresca la página.
2. Verifica en la sección **"Marcar todas como leídas"** que no la hayas leído antes.
3. Contacta al administrador si persiste.

## 11.6 Olvidé mi correo de acceso

Contacta directamente a **Servicios Escolares** o al **Administrador** del sistema. Ellos pueden verificar tu correo registrado.

---

# 12. Glosario

| Término | Definición |
|---|---|
| **Cuatrimestre** | Periodo académico de 4 meses. Las carreras tipo cuatrimestre tienen 10 cuatrimestres en total. |
| **Semestre** | Periodo académico de 6 meses. Las carreras tipo semestre tienen 7 semestres en total. |
| **Matrícula** | Identificador único del alumno (formato: `[CLAVE-CARRERA][AÑO][NÚMERO]`, ej. `DSM20260001`). |
| **Kárdex** | Documento oficial con el historial completo de calificaciones del alumno. |
| **Voucher / Váucher** | Comprobante de pago en formato PDF que el alumno sube por cada cuatrimestre. |
| **Constancia** | Documento oficial generado por Servicios Escolares (de estudios, calificaciones, etc.). |
| **Inscripción** | Registro que vincula a un alumno con un grupo en un ciclo escolar. |
| **Grupo** | Conjunto de alumnos que cursan juntos un cuatrimestre. Tiene clave única (ej. `DSM-1A`). |
| **Tutor** | Docente asignado al acompañamiento académico de un grupo o alumno específico. |
| **Director de carrera** | Docente que coordina académicamente una carrera completa. |
| **Personal de SE** | Persona de Servicios Escolares responsable de administrar una o varias carreras. |
| **Cédula profesional** | Documento oficial que acredita los estudios profesionales de un docente o director. |
| **Estatus** | Estado actual del alumno: Activo, Baja temporal o Baja definitiva. |
| **Semáforo académico** | Indicador visual del rendimiento del alumno: 🟢 verde (≥8.5), 🟡 ámbar (7-8.49), 🔴 rojo (<7). |

---

## Contacto y soporte

Si tienes dudas, problemas técnicos o sugerencias sobre el sistema:

- **Correo de soporte:** soporte@sigea.tudominio.edu.mx
- **Teléfono:** (XXX) XXX-XXXX
- **Horario:** Lunes a viernes de 9:00 a 18:00 hrs.

---

## Créditos

**SIGEA** — Sistema de Gestión Educativa Académica
Desarrollado para la gestión integral de instituciones educativas.

Derechos reservados © 2026.

---

*Fin del manual de usuario.*
