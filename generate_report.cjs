const fs = require("fs");
const {
  Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
  Header, Footer, AlignmentType, LevelFormat,
  HeadingLevel, BorderStyle, WidthType, ShadingType,
  PageNumber, PageBreak
} = require("docx");

// ── Shared styles ──────────────────────────────────────────
const BLUE = "1F4E79";
const LIGHT_BLUE = "D6E4F0";
const GRAY_BG = "F2F2F2";
const WHITE = "FFFFFF";

const border = { style: BorderStyle.SINGLE, size: 1, color: "BBBBBB" };
const borders = { top: border, bottom: border, left: border, right: border };
const noBorders = {
  top: { style: BorderStyle.NONE, size: 0 },
  bottom: { style: BorderStyle.NONE, size: 0 },
  left: { style: BorderStyle.NONE, size: 0 },
  right: { style: BorderStyle.NONE, size: 0 },
};
const cellMargins = { top: 60, bottom: 60, left: 100, right: 100 };

// ── Helpers ────────────────────────────────────────────────
function p(text, opts = {}) {
  const runs = [];
  if (typeof text === "string") {
    runs.push(new TextRun({ text, font: "Arial", size: opts.size || 22, bold: opts.bold, italics: opts.italics, color: opts.color }));
  } else if (Array.isArray(text)) {
    text.forEach(t => runs.push(new TextRun({ ...t, font: t.font || "Arial", size: t.size || 22 })));
  }
  return new Paragraph({
    children: runs,
    spacing: { after: opts.after !== undefined ? opts.after : 120, before: opts.before || 0, line: opts.line || 276 },
    alignment: opts.alignment || AlignmentType.LEFT,
    heading: opts.heading,
    indent: opts.indent,
  });
}

function heading1(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_1,
    children: [new TextRun({ text, font: "Arial", size: 32, bold: true, color: BLUE })],
    spacing: { before: 360, after: 200 },
    border: { bottom: { style: BorderStyle.SINGLE, size: 6, color: BLUE, space: 4 } },
  });
}

function heading2(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_2,
    children: [new TextRun({ text, font: "Arial", size: 26, bold: true, color: "2E75B6" })],
    spacing: { before: 280, after: 160 },
  });
}

function heading3(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_3,
    children: [new TextRun({ text, font: "Arial", size: 24, bold: true, color: "404040" })],
    spacing: { before: 200, after: 120 },
  });
}

function cell(text, opts = {}) {
  const runs = typeof text === "string"
    ? [new TextRun({ text, font: "Arial", size: opts.size || 20, bold: opts.bold, color: opts.fontColor || "333333" })]
    : text.map(t => new TextRun({ ...t, font: t.font || "Arial", size: t.size || 20 }));
  return new TableCell({
    borders,
    width: { size: opts.width || 3000, type: WidthType.DXA },
    shading: opts.shading ? { fill: opts.shading, type: ShadingType.CLEAR } : undefined,
    margins: cellMargins,
    verticalAlign: "center",
    children: [new Paragraph({ children: runs, spacing: { after: 0 }, alignment: opts.alignment || AlignmentType.LEFT })],
  });
}

function headerCell(text, width) {
  return cell(text, { bold: true, shading: BLUE, fontColor: WHITE, width, size: 20 });
}

function makeTable(headers, rows, colWidths) {
  const totalWidth = colWidths.reduce((a, b) => a + b, 0);
  const tableRows = [
    new TableRow({ children: headers.map((h, i) => headerCell(h, colWidths[i])) }),
    ...rows.map((row, ri) =>
      new TableRow({
        children: row.map((c, ci) => cell(c, { width: colWidths[ci], shading: ri % 2 === 0 ? GRAY_BG : WHITE })),
      })
    ),
  ];
  return new Table({ width: { size: totalWidth, type: WidthType.DXA }, columnWidths: colWidths, rows: tableRows });
}

function bullet(text) {
  return new Paragraph({
    numbering: { reference: "bullets", level: 0 },
    children: [new TextRun({ text, font: "Arial", size: 22 })],
    spacing: { after: 60 },
  });
}

function codeBlock(lines) {
  return lines.map(line =>
    new Paragraph({
      children: [new TextRun({ text: line, font: "Consolas", size: 18, color: "333333" })],
      spacing: { after: 20 },
      shading: { type: ShadingType.CLEAR, fill: "F5F5F5" },
      indent: { left: 360 },
    })
  );
}

// ── COVER PAGE ─────────────────────────────────────────────
const coverPage = [
  p("", { after: 2400 }),
  p("", { after: 2400 }),
  new Paragraph({
    children: [new TextRun({ text: "REPORTE TECNICO", font: "Arial", size: 56, bold: true, color: BLUE })],
    alignment: AlignmentType.CENTER,
    spacing: { after: 80 },
  }),
  new Paragraph({
    children: [new TextRun({ text: "SIGEA", font: "Arial", size: 72, bold: true, color: BLUE })],
    alignment: AlignmentType.CENTER,
    spacing: { after: 200 },
  }),
  new Paragraph({
    border: { bottom: { style: BorderStyle.SINGLE, size: 8, color: BLUE, space: 1 } },
    children: [],
    spacing: { after: 200 },
  }),
  new Paragraph({
    children: [new TextRun({ text: "Sistema de Gestion Educativa Academica", font: "Arial", size: 32, color: "555555" })],
    alignment: AlignmentType.CENTER,
    spacing: { after: 600 },
  }),
  new Paragraph({
    children: [new TextRun({ text: "Modulo: Pruebas y entrega del portafolio", font: "Arial", size: 24, color: "666666" })],
    alignment: AlignmentType.CENTER,
    spacing: { after: 120 },
  }),
  new Paragraph({
    children: [new TextRun({ text: "Fecha: 09 de abril de 2026", font: "Arial", size: 24, color: "666666" })],
    alignment: AlignmentType.CENTER,
    spacing: { after: 120 },
  }),
  new Paragraph({
    children: [new TextRun({ text: "Equipo SIGEA", font: "Arial", size: 24, color: "666666" })],
    alignment: AlignmentType.CENTER,
    spacing: { after: 200 },
  }),
  new Paragraph({ children: [new PageBreak()] }),
];

// ── INDEX / TABLE OF CONTENTS ──────────────────────────────
function tocEntry(num, title, page, opts = {}) {
  const level = opts.level || 0;
  const indent = level === 0 ? 0 : (level === 1 ? 600 : 1200);
  const fontSize = level === 0 ? 22 : 20;
  const isBold = level === 0;
  const numWidth = level === 0 ? 800 : 700;

  return new Paragraph({
    tabStops: [
      { type: "right", position: 9000, leader: "dot" },
    ],
    indent: { left: indent },
    spacing: { after: level === 0 ? 120 : 60 },
    children: [
      new TextRun({ text: num ? num + "  " : "", font: "Arial", size: fontSize, bold: isBold, color: isBold ? BLUE : "555555" }),
      new TextRun({ text: title, font: "Arial", size: fontSize, bold: isBold, color: isBold ? "333333" : "555555" }),
      new TextRun({ text: "\t" + page, font: "Arial", size: fontSize, bold: isBold, color: "666666" }),
    ],
  });
}

const indexPage = [
  new Paragraph({
    children: [new TextRun({ text: "INDICE", font: "Arial", size: 40, bold: true, color: BLUE })],
    alignment: AlignmentType.CENTER,
    spacing: { before: 240, after: 200 },
    border: { bottom: { style: BorderStyle.SINGLE, size: 8, color: BLUE, space: 4 } },
  }),
  p("", { after: 240 }),
  tocEntry("", "Portada", "1"),
  tocEntry("", "Introduccion", "2"),
  tocEntry("1.", "Framework seleccionado y justificacion", "3"),
  tocEntry("1.1", "Stack Tecnologico", "3", { level: 1 }),
  tocEntry("1.2", "Justificacion", "3", { level: 1 }),
  tocEntry("2.", "Arquitectura de la API", "4"),
  tocEntry("2.1", "Estructura en capas", "4", { level: 1 }),
  tocEntry("2.2", "Modulos implementados", "4", { level: 1 }),
  tocEntry("2.3", "Servicios de negocio (Service Layer)", "4", { level: 1 }),
  tocEntry("3.", "Endpoints principales (API interna)", "5"),
  tocEntry("3.1", "Chatbot IA (multi-rol)", "5", { level: 1 }),
  tocEntry("3.2", "Ollama local (consumido internamente)", "5", { level: 1 }),
  tocEntry("3.3", "Endpoints CRUD principales", "5", { level: 1 }),
  tocEntry("4.", "Buenas practicas aplicadas", "6"),
  tocEntry("4.1", "Seguridad", "6", { level: 1 }),
  tocEntry("4.2", "Calidad de codigo", "6", { level: 1 }),
  tocEntry("4.3", "Correcciones de logica aplicadas (12 bugs)", "6", { level: 1 }),
  tocEntry("5.", "Chatbot IA \u2014 Arquitectura dual", "7"),
  tocEntry("5.1", "Patron implementado", "7", { level: 1 }),
  tocEntry("5.2", "Ventajas del modo local (Ollama)", "7", { level: 1 }),
  tocEntry("5.3", "Privacidad por rol", "7", { level: 1 }),
  tocEntry("6.", "Pruebas con Postman", "8"),
  tocEntry("6.1", "Matriz de casos de prueba", "8", { level: 1 }),
  tocEntry("6.2", "Prueba del chatbot local (Ollama)", "8", { level: 1 }),
  tocEntry("7.", "Configuracion del entorno", "9"),
  tocEntry("7.1", "Archivo .env (variables clave)", "9", { level: 1 }),
  tocEntry("7.2", "Comandos de instalacion", "9", { level: 1 }),
  tocEntry("8.", "Hardware de prueba", "10"),
  tocEntry("9.", "Repositorio y evidencias", "10"),
  tocEntry("10.", "Conclusiones", "11"),
  new Paragraph({ children: [new PageBreak()] }),
];

// ── INTRODUCTION ───────────────────────────────────────────
const introduction = [
  heading1("Introduccion"),
  p("En el contexto educativo actual, la transformacion digital se ha convertido en un eje estrategico para las instituciones de educacion superior. La gestion de informacion academica \u2014calificaciones, inscripciones, asistencia, evaluaciones docentes y tramites administrativos\u2014 demanda sistemas robustos, seguros y escalables que permitan centralizar procesos antes fragmentados en hojas de calculo, documentos fisicos o aplicaciones aisladas."),
  p("SIGEA (Sistema de Gestion Educativa Academica) surge como respuesta a esta necesidad: una plataforma web integral desarrollada en Laravel que unifica la operacion academica de una institucion educativa en un solo entorno digital. El sistema atiende a cuatro tipos de usuarios \u2014alumnos, docentes, directores de carrera y personal de servicios escolares\u2014 ofreciendo a cada uno un panel personalizado con las funciones correspondientes a su rol, bajo un esquema de autenticacion y autorizacion granular que garantiza la privacidad y confidencialidad de los datos."),
  p("Entre las funcionalidades principales destacan: la gestion de kardex y generacion de constancias en PDF, la captura y consulta de calificaciones y asistencia, el control de horas culturales y deportivas (ACUDE), la evaluacion docente por parte de los alumnos, y la administracion completa de carreras, materias, grupos y horarios. Adicionalmente, el sistema incorpora un asistente virtual basado en Inteligencia Artificial (chatbot SIGEA) capaz de ejecutarse tanto mediante la API de Groq en la nube como de forma local utilizando el modelo Llama 3.1 a traves de Ollama, lo que permite eliminar la dependencia de servicios externos y reforzar la proteccion de los datos sensibles."),
  p("El presente reporte documenta el proceso tecnico de desarrollo, integracion y prueba de la API del sistema, siguiendo buenas practicas de ingenieria de software: arquitectura en capas (Controllers \u2192 Services \u2192 Models), validacion exhaustiva de entradas, manejo seguro de sesiones y contrasenas, control de acceso basado en roles mediante Spatie Permission, y pruebas funcionales ejecutadas con Postman. Se incluyen ademas las correcciones de logica aplicadas durante la fase de depuracion y la justificacion de cada decision tecnologica, con el objetivo de presentar un portafolio de evidencias completo que respalde la calidad, estabilidad y escalabilidad del sistema desarrollado."),
  new Paragraph({ children: [new PageBreak()] }),
];

// ── SECTION 1: FRAMEWORK ──────────────────────────────────
const section1 = [
  heading1("1. Framework seleccionado y justificacion"),
  heading2("1.1 Stack Tecnologico"),
  makeTable(
    ["Capa", "Tecnologia", "Version"],
    [
      ["Backend", "Laravel (PHP)", "11.x"],
      ["Frontend", "Blade + Tailwind CSS + Vite", "-"],
      ["Base de Datos", "MySQL (MariaDB/XAMPP)", "10.4+"],
      ["Motor de plantillas", "Blade", "Nativo Laravel"],
      ["Autorizacion", "Spatie Laravel Permission", "6.x"],
      ["Generacion PDF", "barryvdh/laravel-dompdf", "3.x"],
      ["Chatbot IA Local", "Ollama + Llama 3.1 (4.9 GB)", "latest"],
      ["Chatbot IA Nube", "Groq API (fallback)", "llama-3.3-70b-versatile"],
    ],
    [2500, 4500, 2360]
  ),
  heading2("1.2 Justificacion"),
  p([{ text: "Laravel", bold: true, size: 22 }, { text: " fue seleccionado por:", size: 22 }]),
  bullet("Arquitectura MVC nativa que facilita la separacion en capas (Controllers \u2192 Services \u2192 Models)."),
  bullet("Ecosistema maduro: Eloquent ORM, sistema de migraciones, Blade, autenticacion nativa."),
  bullet("Seguridad por defecto: CSRF, XSS escape en Blade, hash bcrypt, middleware de roles."),
  bullet("Spatie Permission permite control granular con 4 roles (alumno, docente, director_carrera, servicios_escolares)."),
  p(""),
  p([{ text: "MySQL", bold: true, size: 22 }, { text: " se eligio por su disponibilidad en XAMPP y compatibilidad total con Eloquent.", size: 22 }]),
  p([{ text: "Tailwind CSS", bold: true, size: 22 }, { text: " por su enfoque utility-first y la velocidad de prototipado de interfaces.", size: 22 }]),
  p([{ text: "Ollama + Llama 3.1", bold: true, size: 22 }, { text: " para ejecucion local del chatbot, eliminando dependencia de APIs externas y consumo de tokens, cumpliendo requisitos de privacidad de datos academicos.", size: 22 }]),
  new Paragraph({ children: [new PageBreak()] }),
];

// ── SECTION 2: ARCHITECTURE ───────────────────────────────
const section2 = [
  heading1("2. Arquitectura de la API"),
  heading2("2.1 Estructura en capas"),
  makeTable(
    ["Capa", "Ubicacion", "Funcion"],
    [
      ["Vistas Blade", "resources/views/", "Presentacion"],
      ["Controllers", "app/Http/Controllers/", "Logica HTTP"],
      ["Services", "app/Services/", "Logica de negocio"],
      ["Models (22)", "app/Models/", "Eloquent ORM"],
      ["MySQL (22 tablas)", "Base de datos", "Persistencia"],
    ],
    [2500, 4000, 2860]
  ),
  heading2("2.2 Modulos implementados"),
  makeTable(
    ["Modulo", "Rutas", "Controllers", "Vistas"],
    [
      ["Alumno", "14", "12", "12"],
      ["Docente", "11", "8", "11"],
      ["Director de Carrera", "24", "11", "18"],
      ["Servicios Escolares", "40+", "15", "35+"],
      ["Chatbot Multi-rol", "1", "1", "1 (componente)"],
    ],
    [3500, 1800, 2000, 2060]
  ),
  heading2("2.3 Servicios de negocio (Service Layer)"),
  makeTable(
    ["Service", "Responsabilidad"],
    [
      ["EstadisticasCarreraService", "Indices de aprobacion/reprobacion, semaforo, evaluacion docente"],
      ["GrupoService", "Gestion de grupos, asignacion de horarios y tutores"],
      ["CalificacionService", "Registro de calificaciones, boletas, reportes"],
      ["AsistenciaService", "Control de asistencia, reportes PDF"],
      ["KardexService", "Historial academico completo, calculo de promedio, PDF"],
    ],
    [3500, 5860]
  ),
  new Paragraph({ children: [new PageBreak()] }),
];

// ── SECTION 3: ENDPOINTS ──────────────────────────────────
const section3 = [
  heading1("3. Endpoints principales (API interna)"),
  heading2("3.1 Chatbot IA (multi-rol)"),
  p("El chatbot se expone en 4 endpoints, uno por cada rol del sistema:"),
  ...codeBlock([
    "POST /alumno/chatbot",
    "POST /docente/chatbot",
    "POST /director/chatbot",
    "POST /servicios/chatbot",
  ]),
  p(""),
  heading3("Request"),
  ...codeBlock([
    '{',
    '  "mensaje": "\u00bfCual es mi promedio?"',
    '}',
  ]),
  heading3("Response"),
  ...codeBlock([
    '{',
    '  "respuesta": "Tu promedio general actual es <b>8.7</b>. \u00a1Sigue asi!"',
    '}',
  ]),
  heading2("3.2 Ollama local (consumido internamente)"),
  ...codeBlock([
    "POST http://localhost:11434/v1/chat/completions",
    "Content-Type: application/json",
    "",
    '{',
    '  "model": "llama3.1:latest",',
    '  "messages": [',
    '    {"role": "system", "content": "Contexto del rol..."},',
    '    {"role": "user", "content": "Mensaje del usuario"}',
    '  ],',
    '  "temperature": 0.7,',
    '  "max_tokens": 1024,',
    '  "stream": false',
    '}',
  ]),
  heading2("3.3 Endpoints CRUD principales"),
  makeTable(
    ["Metodo", "Ruta", "Descripcion"],
    [
      ["GET", "/servicios/alumnos", "Listar alumnos"],
      ["POST", "/servicios/alumnos", "Crear alumno"],
      ["PUT", "/servicios/alumnos/{id}", "Actualizar alumno"],
      ["DELETE", "/servicios/alumnos/{id}", "Eliminar alumno"],
      ["GET", "/alumno/kardex/pdf", "Generar PDF de kardex"],
      ["POST", "/alumno/evaluacion-docente", "Enviar evaluacion"],
      ["GET", "/director/indice-aprobacion", "Estadisticas de aprobacion"],
    ],
    [1500, 4000, 3860]
  ),
  new Paragraph({ children: [new PageBreak()] }),
];

// ── SECTION 4: BEST PRACTICES ─────────────────────────────
const section4 = [
  heading1("4. Buenas practicas aplicadas"),
  heading2("4.1 Seguridad"),
  makeTable(
    ["Practica", "Implementacion"],
    [
      ["Autenticacion", "Laravel Auth + sesiones"],
      ["Hash de contrasenas", "bcrypt (10 rounds)"],
      ["CSRF Protection", "Token en todos los formularios y AJAX"],
      ["Autorizacion por roles", "middleware('role:alumno'), etc."],
      ["SQL Injection", "Eloquent ORM (consultas parametrizadas)"],
      ["XSS", "Escape automatico de Blade ({{ }})"],
      ["Variables sensibles", "Archivo .env excluido del repositorio"],
      ["Validacion de inputs", "$request->validate([...])"],
      ["Privacidad del chatbot", "Contexto aislado por rol"],
    ],
    [3500, 5860]
  ),
  heading2("4.2 Calidad de codigo"),
  bullet("Nombres descriptivos: contextoAlumno(), llamarOllama(), detectarRol()."),
  bullet("Separacion de responsabilidades: Controllers delgados, logica en Services."),
  bullet("Null-safety: Operadores ?-> y ?? en vistas para evitar crashes."),
  bullet("Manejo de errores: try/catch con logging y fallback local."),
  bullet("Configuracion externalizada: .env para drivers, URLs y modelos."),
  heading2("4.3 Correcciones de logica aplicadas (12 bugs corregidos)"),
  makeTable(
    ["#", "Archivo", "Tipo de error"],
    [
      ["1", "LoginController.php", "Metodo inexistente dashboardRoute()"],
      ["2", "RedirectByRole.php", "Mismo metodo inexistente"],
      ["3", "Alumno/CalificacionesController", "[] con isNotEmpty() \u2192 collect()"],
      ["4-5", "Servicios/DocentesController", "Codigo muerto tipo_contrato"],
      ["6-7", "Director/views", "Data access mismatch (array vs objeto)"],
      ["8", "Director/HorariosController", "Validacion faltante dia_semana"],
      ["9-10", "Docente/Controllers", "Null safety en horarios"],
      ["11", "Docente/evaluacion-resultados", "Campo mal nombrado comentario"],
      ["12", "Docente/EvaluacionResultadosController", "Filtro de ciclo ignorado"],
    ],
    [800, 4200, 4360]
  ),
  new Paragraph({ children: [new PageBreak()] }),
];

// ── SECTION 5: CHATBOT ARCHITECTURE ───────────────────────
const section5 = [
  heading1("5. Chatbot IA \u2014 Arquitectura dual"),
  heading2("5.1 Patron implementado"),
  p("El controlador ChatbotController utiliza un patron Strategy para seleccionar el driver de IA:"),
  ...codeBlock([
    "Controller \u2192 llamarIA() \u2192 \u00bfCHATBOT_DRIVER?",
    "                           \u251c\u2500\u2500 local \u2192 llamarOllama() \u2192 localhost:11434",
    "                           \u2514\u2500\u2500 groq  \u2192 llamarGroq()  \u2192 api.groq.com",
  ]),
  heading2("5.2 Ventajas del modo local (Ollama)"),
  bullet("Sin costo por tokens."),
  bullet("Sin internet requerido."),
  bullet("Privacidad total: los datos academicos no salen del servidor."),
  bullet("Cumplimiento: ideal para datos sensibles educativos."),
  heading2("5.3 Privacidad por rol"),
  p("Cada rol recibe unicamente el contexto que le corresponde, nunca datos de otros usuarios:"),
  makeTable(
    ["Rol", "Contexto inyectado", "Datos que NO ve"],
    [
      ["Alumno", "Sus datos propios (promedio, horario, ACUDE)", "Datos de otros alumnos o docentes"],
      ["Docente", "Sus grupos y materias", "Datos personales de alumnos"],
      ["Director", "Estadisticas agregadas de SU carrera", "Datos individuales"],
      ["Servicios", "Estadisticas generales del sistema", "Datos personales individuales"],
    ],
    [1800, 4200, 3360]
  ),
  new Paragraph({ children: [new PageBreak()] }),
];

// ── SECTION 6: POSTMAN TESTS ──────────────────────────────
const section6 = [
  heading1("6. Pruebas con Postman"),
  heading2("6.1 Matriz de casos de prueba"),
  makeTable(
    ["ID", "Caso de prueba", "Metodo", "Endpoint", "Resultado"],
    [
      ["CP-01", "Login exitoso", "POST", "/login", "302 Redirect (PASS)"],
      ["CP-02", "Login credenciales invalidas", "POST", "/login", "422 Validation (PASS)"],
      ["CP-03", "Acceso sin autenticacion", "GET", "/alumno/dashboard", "302 \u2192 login (PASS)"],
      ["CP-04", "Chatbot Groq (nube)", "POST", "/alumno/chatbot", "200 OK 889ms (PASS)"],
      ["CP-05", "Acceso rol incorrecto", "GET", "/servicios/dashboard", "403 Forbidden (PASS)"],
      ["CP-06", "Generar PDF kardex", "GET", "/alumno/kardex/pdf", "200 OK pdf (PASS)"],
      ["CP-07", "Crear alumno", "POST", "/servicios/alumnos", "302 + DB row (PASS)"],
    ],
    [900, 2800, 1100, 2200, 2360]
  ),
  heading2("6.2 Prueba del chatbot local (Ollama)"),
  heading3("Request"),
  ...codeBlock([
    'curl http://localhost:11434/v1/chat/completions',
    '  -d "{\\"model\\":\\"llama3.1:latest\\",',
    '       \\"messages\\":[{\\"role\\":\\"user\\",\\"content\\":\\"hola\\"}]}"',
  ]),
  heading3("Response"),
  ...codeBlock([
    '{',
    '  "id": "chatcmpl-998",',
    '  "object": "chat.completion",',
    '  "model": "llama3.1:latest",',
    '  "choices": [{',
    '    "message": {',
    '      "role": "assistant",',
    '      "content": "\u00bfEn que puedo ayudarte hoy?"',
    '    },',
    '    "finish_reason": "stop"',
    '  }],',
    '  "usage": {',
    '    "prompt_tokens": 12,',
    '    "completion_tokens": 9,',
    '    "total_tokens": 21',
    '  }',
    '}',
  ]),
  new Paragraph({ children: [new PageBreak()] }),
];

// ── SECTION 7: ENVIRONMENT ────────────────────────────────
const section7 = [
  heading1("7. Configuracion del entorno"),
  heading2("7.1 Archivo .env (variables clave)"),
  ...codeBlock([
    "APP_NAME=SIGEA",
    "APP_ENV=local",
    "APP_DEBUG=true",
    "",
    "DB_CONNECTION=mysql",
    "DB_HOST=127.0.0.1",
    "DB_PORT=3306",
    "DB_DATABASE=sigea_bd",
    "DB_USERNAME=root",
    "DB_PASSWORD=",
    "",
    "# Chatbot Driver: 'local' (Ollama) o 'groq' (nube)",
    "CHATBOT_DRIVER=local",
    "",
    "# Ollama local",
    "OLLAMA_URL=http://localhost:11434/v1/chat/completions",
    "OLLAMA_MODEL=llama3.1:latest",
    "",
    "# Groq nube (fallback)",
    "GROQ_API_KEY=gsk_...",
    "GROQ_MODEL=llama-3.3-70b-versatile",
  ]),
  heading2("7.2 Comandos de instalacion"),
  ...codeBlock([
    "# Backend",
    "composer install",
    "php artisan key:generate",
    "php artisan migrate --seed",
    "",
    "# Frontend",
    "npm install",
    "npm run dev",
    "",
    "# IA Local",
    "ollama pull llama3.1",
    "ollama serve",
    "",
    "# Servidor",
    "php artisan serve",
  ]),
  new Paragraph({ children: [new PageBreak()] }),
];

// ── SECTION 8: HARDWARE ───────────────────────────────────
const section8 = [
  heading1("8. Hardware de prueba"),
  p([{ text: "Equipo: ", bold: true, size: 22 }, { text: "ASUS TUF Gaming A15", size: 22 }]),
  makeTable(
    ["Componente", "Especificacion"],
    [
      ["CPU", "AMD Ryzen 5 7535HS"],
      ["GPU", "NVIDIA GeForce RTX 4050 (6GB GDDR6)"],
      ["RAM", "32 GB DDR5"],
      ["Almacenamiento", "340.9 / 449.5 GB utilizados"],
      ["Tiempo respuesta Ollama", "5-15 segundos promedio"],
    ],
    [3500, 5860]
  ),
];

// ── SECTION 9: REPOSITORY ─────────────────────────────────
const section9 = [
  heading1("9. Repositorio y evidencias"),
  makeTable(
    ["Elemento", "Detalle"],
    [
      ["Repositorio", "https://github.com/MrpotetoR/SIGEA_Proyecto"],
      ["Rama principal", "main"],
      ["Total de archivos", "200+"],
      ["Commits totales", "20+"],
      ["Lenguaje principal", "PHP (Laravel)"],
    ],
    [3500, 5860]
  ),
];

// ── SECTION 10: CONCLUSIONS ───────────────────────────────
const section10 = [
  heading1("10. Conclusiones"),
  p("El sistema SIGEA implementa una API REST interna robusta sobre Laravel, con separacion clara en capas, seguridad por roles, validacion exhaustiva y un chatbot IA con arquitectura dual (local/nube) que respeta la privacidad de los datos academicos."),
  p("La migracion de Groq (nube) a Ollama (local con Llama 3.1) demuestra flexibilidad arquitectonica: un solo cambio de variable en .env alterna entre ambos modos, sin modificar controllers ni vistas. Esto garantiza continuidad del servicio y cumplimiento de requisitos de privacidad."),
  p("Las 12 correcciones de logica aplicadas, junto con los 7 casos de prueba funcionales exitosos, validan la estabilidad del sistema para su entrega como portafolio de evidencias completo."),
];

// ── DOCUMENT ASSEMBLY ─────────────────────────────────────
const doc = new Document({
  numbering: {
    config: [
      {
        reference: "bullets",
        levels: [{
          level: 0,
          format: LevelFormat.BULLET,
          text: "\u2022",
          alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: 720, hanging: 360 } } },
        }],
      },
    ],
  },
  styles: {
    default: {
      document: { run: { font: "Arial", size: 22 } },
    },
    paragraphStyles: [
      {
        id: "Heading1", name: "Heading 1", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { size: 32, bold: true, font: "Arial", color: BLUE },
        paragraph: { spacing: { before: 360, after: 200 }, outlineLevel: 0 },
      },
      {
        id: "Heading2", name: "Heading 2", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { size: 26, bold: true, font: "Arial", color: "2E75B6" },
        paragraph: { spacing: { before: 280, after: 160 }, outlineLevel: 1 },
      },
      {
        id: "Heading3", name: "Heading 3", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { size: 24, bold: true, font: "Arial", color: "404040" },
        paragraph: { spacing: { before: 200, after: 120 }, outlineLevel: 2 },
      },
    ],
  },
  sections: [
    {
      properties: {
        page: {
          size: { width: 12240, height: 15840 },
          margin: { top: 1440, right: 1440, bottom: 1440, left: 1440 },
        },
      },
      headers: {
        default: new Header({
          children: [
            new Paragraph({
              children: [
                new TextRun({ text: "SIGEA \u2014 Reporte Tecnico", font: "Arial", size: 16, color: "999999" }),
              ],
              alignment: AlignmentType.RIGHT,
              border: { bottom: { style: BorderStyle.SINGLE, size: 2, color: "CCCCCC", space: 4 } },
            }),
          ],
        }),
      },
      footers: {
        default: new Footer({
          children: [
            new Paragraph({
              children: [
                new TextRun({ text: "Pagina ", font: "Arial", size: 16, color: "999999" }),
                new TextRun({ children: [PageNumber.CURRENT], font: "Arial", size: 16, color: "999999" }),
              ],
              alignment: AlignmentType.CENTER,
              border: { top: { style: BorderStyle.SINGLE, size: 2, color: "CCCCCC", space: 4 } },
            }),
          ],
        }),
      },
      children: [
        ...coverPage,
        ...indexPage,
        ...introduction,
        ...section1,
        ...section2,
        ...section3,
        ...section4,
        ...section5,
        ...section6,
        ...section7,
        ...section8,
        ...section9,
        ...section10,
      ],
    },
  ],
});

const OUTPUT = "C:\\Users\\Yosra\\SIGEA_Proyecto\\Reporte_Tecnico_SIGEA.docx";

Packer.toBuffer(doc).then(buffer => {
  fs.writeFileSync(OUTPUT, buffer);
  console.log("Documento generado: " + OUTPUT);
}).catch(err => {
  console.error("Error:", err);
  process.exit(1);
});
