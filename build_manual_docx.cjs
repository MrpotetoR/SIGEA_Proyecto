/**
 * Convertidor del MANUAL_DE_USUARIO.md a Manual_de_Usuario_SIGEA.docx
 * Uso: node build_manual_docx.cjs
 */

const fs = require('fs');
const path = require('path');
const {
    Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
    Header, Footer, AlignmentType, PageOrientation, LevelFormat,
    TabStopType, TabStopPosition, HeadingLevel, BorderStyle, WidthType,
    ShadingType, VerticalAlign, PageNumber, PageBreak, TableOfContents
} = require('docx');

// ─── Configuración global ────────────────────────────────────────
const PAGE_WIDTH = 12240;   // US Letter
const PAGE_HEIGHT = 15840;
const MARGIN = 1440;        // 1 pulgada
const CONTENT_WIDTH = PAGE_WIDTH - MARGIN * 2; // 9360 DXA

const COLORS = {
    primary: '0606F0',
    primaryDark: '04276B',
    grayText: '6B7280',
    grayLight: 'F3F4F6',
    headerBg: '1A4FC7',
    border: 'CCCCCC',
    captureBg: 'FFF7E6',
    captureText: '92400E',
    keyBg: 'EFF6FF',
    keyText: '1E40AF',
};

// ─── Utilitarios para parsear texto inline (negritas, cursivas, código, links, emojis) ───

/**
 * Limpia emojis problemáticos (los reemplaza por texto descriptivo).
 * Word los soporta pero algunos pueden romper la fuente predeterminada.
 */
function cleanEmojis(text) {
    return text;  // Los dejamos — Word los renderiza con la fuente apropiada.
}

/**
 * Convierte una línea de texto Markdown inline a un array de TextRun.
 * Soporta: **negrita**, *cursiva*, `código`, [texto](url) → texto.
 */
function parseInline(line, baseStyle = {}) {
    const runs = [];
    if (!line) return [new TextRun({ text: '', ...baseStyle })];

    // Reemplazar links [texto](url) por solo el texto (los URLs van entre paréntesis para preservar info)
    line = line.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '$1');

    // Tokenizar negritas/cursivas/código
    const regex = /(\*\*[^*]+\*\*|\*[^*]+\*|`[^`]+`)/g;
    let lastIndex = 0;
    let match;

    while ((match = regex.exec(line)) !== null) {
        if (match.index > lastIndex) {
            runs.push(new TextRun({ text: cleanEmojis(line.slice(lastIndex, match.index)), ...baseStyle }));
        }
        const token = match[0];
        if (token.startsWith('**')) {
            runs.push(new TextRun({ text: cleanEmojis(token.slice(2, -2)), bold: true, ...baseStyle }));
        } else if (token.startsWith('`')) {
            runs.push(new TextRun({ text: cleanEmojis(token.slice(1, -1)), font: 'Consolas', ...baseStyle }));
        } else if (token.startsWith('*')) {
            runs.push(new TextRun({ text: cleanEmojis(token.slice(1, -1)), italics: true, ...baseStyle }));
        }
        lastIndex = match.index + token.length;
    }
    if (lastIndex < line.length) {
        runs.push(new TextRun({ text: cleanEmojis(line.slice(lastIndex)), ...baseStyle }));
    }
    return runs.length > 0 ? runs : [new TextRun({ text: '', ...baseStyle })];
}

// ─── Construcción de tablas Markdown → Word ──────────────────────
function buildTable(headerRow, rows) {
    const numCols = headerRow.length;
    const colWidth = Math.floor(CONTENT_WIDTH / numCols);
    const columnWidths = Array(numCols).fill(colWidth);
    columnWidths[numCols - 1] = CONTENT_WIDTH - colWidth * (numCols - 1); // ajuste último

    const border = { style: BorderStyle.SINGLE, size: 4, color: COLORS.border };
    const borders = { top: border, bottom: border, left: border, right: border, insideHorizontal: border, insideVertical: border };

    const tableRows = [
        new TableRow({
            tableHeader: true,
            children: headerRow.map((cell, idx) => new TableCell({
                borders,
                width: { size: columnWidths[idx], type: WidthType.DXA },
                shading: { fill: COLORS.headerBg, type: ShadingType.CLEAR },
                margins: { top: 100, bottom: 100, left: 140, right: 140 },
                verticalAlign: VerticalAlign.CENTER,
                children: [new Paragraph({
                    children: parseInline(cell, { bold: true, color: 'FFFFFF', size: 20 }),
                    spacing: { before: 0, after: 0 },
                })],
            })),
        }),
        ...rows.map((row, rowIdx) => new TableRow({
            children: row.map((cell, idx) => new TableCell({
                borders,
                width: { size: columnWidths[idx], type: WidthType.DXA },
                shading: rowIdx % 2 === 0
                    ? { fill: 'FFFFFF', type: ShadingType.CLEAR }
                    : { fill: 'F9FAFB', type: ShadingType.CLEAR },
                margins: { top: 80, bottom: 80, left: 140, right: 140 },
                verticalAlign: VerticalAlign.CENTER,
                children: cell.split('<br>').map(line =>
                    new Paragraph({
                        children: parseInline(line.trim(), { size: 20 }),
                        spacing: { before: 0, after: 0 },
                    })
                ),
            })),
        })),
    ];

    return new Table({
        width: { size: CONTENT_WIDTH, type: WidthType.DXA },
        columnWidths,
        rows: tableRows,
    });
}

// ─── Parser principal del Markdown ────────────────────────────────
function parseMarkdown(md) {
    const lines = md.split(/\r?\n/);
    const elements = [];
    let i = 0;

    while (i < lines.length) {
        const line = lines[i];

        // Saltar línea vacía
        if (line.trim() === '') {
            i++;
            continue;
        }

        // Separador horizontal --- (lo ignoramos)
        if (/^-{3,}$/.test(line.trim())) {
            i++;
            continue;
        }

        // Encabezados
        if (line.startsWith('# ')) {
            elements.push({ type: 'h1', text: line.substring(2).trim() });
            i++;
            continue;
        }
        if (line.startsWith('## ')) {
            elements.push({ type: 'h2', text: line.substring(3).trim() });
            i++;
            continue;
        }
        if (line.startsWith('### ')) {
            elements.push({ type: 'h3', text: line.substring(4).trim() });
            i++;
            continue;
        }
        if (line.startsWith('#### ')) {
            elements.push({ type: 'h4', text: line.substring(5).trim() });
            i++;
            continue;
        }

        // Tabla Markdown: detectar si la línea actual y la siguiente parecen tabla
        if (line.startsWith('|') && i + 1 < lines.length && /^\|[\s|:-]+\|/.test(lines[i + 1])) {
            const headerCells = line.split('|').slice(1, -1).map(c => c.trim());
            i += 2; // saltar header + separador
            const rows = [];
            while (i < lines.length && lines[i].startsWith('|')) {
                const cells = lines[i].split('|').slice(1, -1).map(c => c.trim());
                rows.push(cells);
                i++;
            }
            elements.push({ type: 'table', header: headerCells, rows });
            continue;
        }

        // Blockquote — distinguir captura, nota o tip
        if (line.startsWith('> ')) {
            const blockLines = [];
            while (i < lines.length && lines[i].startsWith('>')) {
                blockLines.push(lines[i].replace(/^>\s?/, ''));
                i++;
            }
            const blockText = blockLines.join(' ').trim();

            // Detectar tipo de blockquote
            if (blockText.startsWith('📷')) {
                elements.push({ type: 'capture', text: blockText });
            } else if (blockText.startsWith('🔑')) {
                elements.push({ type: 'credentials', text: blockText });
            } else if (blockText.startsWith('⚠️')) {
                elements.push({ type: 'warning', text: blockText });
            } else if (blockText.startsWith('💡')) {
                elements.push({ type: 'tip', text: blockText });
            } else if (blockText.startsWith('📌') || blockText.startsWith('ℹ️')) {
                elements.push({ type: 'info', text: blockText });
            } else if (blockText.startsWith('🚫')) {
                elements.push({ type: 'forbidden', text: blockText });
            } else {
                elements.push({ type: 'note', text: blockText });
            }
            continue;
        }

        // Listas con viñetas (-, *, +) — NO maneja anidado para evitar bucles
        if (/^[\-\*\+]\s+/.test(line)) {
            const items = [];
            while (i < lines.length && /^[\-\*\+]\s+/.test(lines[i])) {
                items.push(lines[i].replace(/^[\-\*\+]\s+/, ''));
                i++;
            }
            elements.push({ type: 'bullets', items });
            continue;
        }

        // Listas numeradas
        if (/^\d+\.\s+/.test(line)) {
            const items = [];
            while (i < lines.length && /^\d+\.\s+/.test(lines[i])) {
                const item = lines[i].replace(/^\d+\.\s+/, '');
                items.push(item);
                i++;
            }
            elements.push({ type: 'numbers', items });
            continue;
        }

        // Bloque de código
        if (line.startsWith('```')) {
            const codeLines = [];
            i++;
            while (i < lines.length && !lines[i].startsWith('```')) {
                codeLines.push(lines[i]);
                i++;
            }
            i++; // cerrar ```
            elements.push({ type: 'code', text: codeLines.join('\n') });
            continue;
        }

        // Párrafo normal
        const paraLines = [line];
        i++;
        while (i < lines.length && lines[i].trim() !== '' &&
               !lines[i].startsWith('#') && !lines[i].startsWith('>') &&
               !lines[i].startsWith('|') && !lines[i].startsWith('```') &&
               !/^[\-\*\+]\s+/.test(lines[i]) && !/^\d+\.\s+/.test(lines[i]) &&
               !/^-{3,}$/.test(lines[i].trim())) {
            paraLines.push(lines[i]);
            i++;
        }
        elements.push({ type: 'paragraph', text: paraLines.join(' ') });
    }
    return elements;
}

// ─── Convertir elementos a docx-js objects ────────────────────────
function elementsToDocx(elements, opts = {}) {
    const docElements = [];
    const skipFirstHeading = opts.skipFirstHeading || false;
    let firstHeadingSkipped = !skipFirstHeading;

    for (const el of elements) {
        switch (el.type) {
            case 'h1':
                if (!firstHeadingSkipped) { firstHeadingSkipped = true; continue; }
                docElements.push(new Paragraph({
                    heading: HeadingLevel.HEADING_1,
                    pageBreakBefore: true,
                    children: parseInline(el.text),
                }));
                break;
            case 'h2':
                docElements.push(new Paragraph({
                    heading: HeadingLevel.HEADING_2,
                    children: parseInline(el.text),
                }));
                break;
            case 'h3':
                docElements.push(new Paragraph({
                    heading: HeadingLevel.HEADING_3,
                    children: parseInline(el.text),
                }));
                break;
            case 'h4':
                docElements.push(new Paragraph({
                    heading: HeadingLevel.HEADING_4,
                    children: parseInline(el.text),
                }));
                break;
            case 'paragraph':
                docElements.push(new Paragraph({
                    children: parseInline(el.text),
                    spacing: { before: 80, after: 120, line: 300 },
                }));
                break;
            case 'capture':
                docElements.push(new Paragraph({
                    children: parseInline(el.text, { italics: true, color: COLORS.captureText, size: 20 }),
                    indent: { left: 360, right: 360 },
                    spacing: { before: 80, after: 80 },
                    border: {
                        left: { style: BorderStyle.SINGLE, size: 24, color: 'F59E0B', space: 8 },
                    },
                    shading: { fill: COLORS.captureBg, type: ShadingType.CLEAR },
                }));
                break;
            case 'credentials':
                docElements.push(new Paragraph({
                    children: parseInline(el.text, { color: COLORS.keyText, size: 22 }),
                    indent: { left: 360, right: 360 },
                    spacing: { before: 120, after: 120 },
                    border: {
                        left: { style: BorderStyle.SINGLE, size: 24, color: COLORS.primary, space: 8 },
                    },
                    shading: { fill: COLORS.keyBg, type: ShadingType.CLEAR },
                }));
                break;
            case 'warning':
                docElements.push(new Paragraph({
                    children: parseInline(el.text, { color: 'B91C1C', size: 21 }),
                    indent: { left: 360, right: 360 },
                    spacing: { before: 100, after: 100 },
                    border: {
                        left: { style: BorderStyle.SINGLE, size: 24, color: 'DC2626', space: 8 },
                    },
                    shading: { fill: 'FEF2F2', type: ShadingType.CLEAR },
                }));
                break;
            case 'tip':
                docElements.push(new Paragraph({
                    children: parseInline(el.text, { color: '047857', size: 21 }),
                    indent: { left: 360, right: 360 },
                    spacing: { before: 100, after: 100 },
                    border: {
                        left: { style: BorderStyle.SINGLE, size: 24, color: '10B981', space: 8 },
                    },
                    shading: { fill: 'F0FDF4', type: ShadingType.CLEAR },
                }));
                break;
            case 'info':
                docElements.push(new Paragraph({
                    children: parseInline(el.text, { color: '1E40AF', size: 21 }),
                    indent: { left: 360, right: 360 },
                    spacing: { before: 100, after: 100 },
                    border: {
                        left: { style: BorderStyle.SINGLE, size: 24, color: '3B82F6', space: 8 },
                    },
                    shading: { fill: 'EFF6FF', type: ShadingType.CLEAR },
                }));
                break;
            case 'forbidden':
                docElements.push(new Paragraph({
                    children: parseInline(el.text, { color: 'B91C1C', size: 21 }),
                    indent: { left: 360 },
                    spacing: { before: 80, after: 80 },
                }));
                break;
            case 'note':
                docElements.push(new Paragraph({
                    children: parseInline(el.text, { italics: true, size: 21 }),
                    indent: { left: 360, right: 360 },
                    spacing: { before: 80, after: 80 },
                }));
                break;
            case 'bullets':
                for (const item of el.items) {
                    docElements.push(new Paragraph({
                        numbering: { reference: 'bullets', level: 0 },
                        children: parseInline(item),
                        spacing: { before: 40, after: 40 },
                    }));
                }
                break;
            case 'numbers':
                for (const item of el.items) {
                    docElements.push(new Paragraph({
                        numbering: { reference: 'numbers', level: 0 },
                        children: parseInline(item),
                        spacing: { before: 40, after: 40 },
                    }));
                }
                break;
            case 'code':
                docElements.push(new Paragraph({
                    children: [new TextRun({ text: el.text, font: 'Consolas', size: 20, color: '1F2937' })],
                    spacing: { before: 100, after: 100 },
                    indent: { left: 360, right: 360 },
                    shading: { fill: COLORS.grayLight, type: ShadingType.CLEAR },
                }));
                break;
            case 'table':
                docElements.push(buildTable(el.header, el.rows));
                docElements.push(new Paragraph({ children: [new TextRun('')], spacing: { after: 120 } }));
                break;
        }
    }
    return docElements;
}

// ─── Cover page ────────────────────────────────────────────────────
function buildCoverPage() {
    return [
        new Paragraph({ children: [new TextRun('')], spacing: { before: 4000 } }),
        new Paragraph({
            children: [new TextRun({
                text: 'MANUAL DE USUARIO',
                bold: true,
                size: 56,
                color: COLORS.primaryDark,
                font: 'Calibri',
            })],
            alignment: AlignmentType.CENTER,
            spacing: { after: 200 },
        }),
        new Paragraph({
            children: [new TextRun({
                text: 'SIGEA',
                bold: true,
                size: 96,
                color: COLORS.primary,
                font: 'Calibri',
            })],
            alignment: AlignmentType.CENTER,
            spacing: { after: 400 },
        }),
        new Paragraph({
            children: [new TextRun({
                text: 'Sistema de Gestión Educativa Académica',
                size: 32,
                color: COLORS.grayText,
                italics: true,
                font: 'Cambria',
            })],
            alignment: AlignmentType.CENTER,
            spacing: { after: 1200 },
        }),
        new Paragraph({
            children: [new TextRun({ text: '___________________________________', color: 'CBD5E1' })],
            alignment: AlignmentType.CENTER,
            spacing: { after: 600 },
        }),
        new Paragraph({
            children: [new TextRun({ text: 'Versión 1.0', size: 28, font: 'Calibri' })],
            alignment: AlignmentType.CENTER,
            spacing: { after: 100 },
        }),
        new Paragraph({
            children: [new TextRun({ text: 'Abril 2026', size: 26, color: COLORS.grayText, font: 'Calibri' })],
            alignment: AlignmentType.CENTER,
            spacing: { after: 200 },
        }),
        new Paragraph({ children: [new PageBreak()] }),
    ];
}

// ─── TOC page ──────────────────────────────────────────────────────
function buildTOCPage() {
    return [
        new Paragraph({
            children: [new TextRun({
                text: 'Tabla de contenidos',
                bold: true,
                size: 40,
                color: COLORS.primaryDark,
                font: 'Calibri',
            })],
            spacing: { before: 200, after: 400 },
        }),
        new TableOfContents('Tabla de contenidos', {
            hyperlink: true,
            headingStyleRange: '1-3',
        }),
        new Paragraph({ children: [new PageBreak()] }),
    ];
}

// ─── Main build ───────────────────────────────────────────────────
async function build() {
    const mdPath = path.join(__dirname, 'MANUAL_DE_USUARIO.md');
    const outPath = path.join(__dirname, 'Manual_de_Usuario_SIGEA.docx');

    console.log('Leyendo Markdown...');
    const md = fs.readFileSync(mdPath, 'utf8');

    console.log('Parseando estructura...');
    const elements = parseMarkdown(md);
    console.log(`  ${elements.length} elementos detectados.`);
    console.log(`  ${elements.filter(e => e.type === 'h1').length} H1, ` +
                `${elements.filter(e => e.type === 'h2').length} H2, ` +
                `${elements.filter(e => e.type === 'h3').length} H3, ` +
                `${elements.filter(e => e.type === 'h4').length} H4`);
    console.log(`  ${elements.filter(e => e.type === 'table').length} tablas, ` +
                `${elements.filter(e => e.type === 'capture').length} marcadores de captura.`);

    console.log('Construyendo documento Word...');

    // Saltar el primer h1 ("Manual de Usuario — SIGEA") y h2 ("Sistema...") porque van en la portada
    let firstH1Removed = false;
    let firstH2Removed = false;
    const filteredElements = elements.filter(el => {
        if (!firstH1Removed && el.type === 'h1') {
            firstH1Removed = true;
            return false;
        }
        if (!firstH2Removed && el.type === 'h2') {
            firstH2Removed = true;
            return false;
        }
        // También quitar el "Índice" manual que está en el .md (lo reemplazará el TOC automático)
        if (el.type === 'h2' && el.text === 'Índice') return false;
        if (el.type === 'numbers' && el.items.length > 5 && el.items[0].includes('Introducción')) return false;
        return true;
    });

    const bodyElements = elementsToDocx(filteredElements);

    const doc = new Document({
        creator: 'SIGEA',
        title: 'Manual de Usuario — SIGEA',
        description: 'Manual de Usuario del Sistema de Gestión Educativa Académica',
        styles: {
            default: {
                document: { run: { font: 'Calibri', size: 22 } },
            },
            paragraphStyles: [
                {
                    id: 'Heading1', name: 'Heading 1', basedOn: 'Normal', next: 'Normal', quickFormat: true,
                    run: { size: 40, bold: true, font: 'Calibri', color: COLORS.primaryDark },
                    paragraph: { spacing: { before: 480, after: 240 }, outlineLevel: 0 },
                },
                {
                    id: 'Heading2', name: 'Heading 2', basedOn: 'Normal', next: 'Normal', quickFormat: true,
                    run: { size: 30, bold: true, font: 'Calibri', color: COLORS.primary },
                    paragraph: { spacing: { before: 320, after: 160 }, outlineLevel: 1 },
                },
                {
                    id: 'Heading3', name: 'Heading 3', basedOn: 'Normal', next: 'Normal', quickFormat: true,
                    run: { size: 26, bold: true, font: 'Calibri', color: '1F2937' },
                    paragraph: { spacing: { before: 240, after: 120 }, outlineLevel: 2 },
                },
                {
                    id: 'Heading4', name: 'Heading 4', basedOn: 'Normal', next: 'Normal', quickFormat: true,
                    run: { size: 24, bold: true, italics: true, font: 'Calibri', color: '374151' },
                    paragraph: { spacing: { before: 160, after: 80 }, outlineLevel: 3 },
                },
            ],
        },
        numbering: {
            config: [
                {
                    reference: 'bullets',
                    levels: [{
                        level: 0,
                        format: LevelFormat.BULLET,
                        text: '•',
                        alignment: AlignmentType.LEFT,
                        style: { paragraph: { indent: { left: 720, hanging: 360 } } },
                    }],
                },
                {
                    reference: 'numbers',
                    levels: [{
                        level: 0,
                        format: LevelFormat.DECIMAL,
                        text: '%1.',
                        alignment: AlignmentType.LEFT,
                        style: { paragraph: { indent: { left: 720, hanging: 360 } } },
                    }],
                },
            ],
        },
        sections: [
            // SECCIÓN 1: Portada (sin header/footer)
            {
                properties: {
                    page: {
                        size: { width: PAGE_WIDTH, height: PAGE_HEIGHT },
                        margin: { top: MARGIN, right: MARGIN, bottom: MARGIN, left: MARGIN },
                    },
                    titlePage: true,
                },
                children: buildCoverPage(),
            },
            // SECCIÓN 2: TOC + cuerpo (con header/footer)
            {
                properties: {
                    page: {
                        size: { width: PAGE_WIDTH, height: PAGE_HEIGHT },
                        margin: { top: MARGIN, right: MARGIN, bottom: MARGIN, left: MARGIN },
                    },
                },
                headers: {
                    default: new Header({
                        children: [new Paragraph({
                            children: [
                                new TextRun({ text: 'Manual de Usuario — SIGEA', size: 18, color: COLORS.grayText, font: 'Calibri' }),
                                new TextRun({ text: '\tSistema de Gestión Educativa Académica', size: 18, color: COLORS.grayText, font: 'Calibri' }),
                            ],
                            tabStops: [{ type: TabStopType.RIGHT, position: TabStopPosition.MAX }],
                            border: {
                                bottom: { style: BorderStyle.SINGLE, size: 6, color: COLORS.primary, space: 4 },
                            },
                        })],
                    }),
                },
                footers: {
                    default: new Footer({
                        children: [new Paragraph({
                            children: [
                                new TextRun({ text: '© 2026 SIGEA — Manual de Usuario', size: 18, color: COLORS.grayText, font: 'Calibri' }),
                                new TextRun({ text: '\tPágina ', size: 18, color: COLORS.grayText, font: 'Calibri' }),
                                new TextRun({ children: [PageNumber.CURRENT], size: 18, color: COLORS.grayText, bold: true, font: 'Calibri' }),
                                new TextRun({ text: ' de ', size: 18, color: COLORS.grayText, font: 'Calibri' }),
                                new TextRun({ children: [PageNumber.TOTAL_PAGES], size: 18, color: COLORS.grayText, font: 'Calibri' }),
                            ],
                            tabStops: [{ type: TabStopType.RIGHT, position: TabStopPosition.MAX }],
                            border: {
                                top: { style: BorderStyle.SINGLE, size: 6, color: COLORS.primary, space: 4 },
                            },
                        })],
                    }),
                },
                children: [
                    ...buildTOCPage(),
                    ...bodyElements,
                ],
            },
        ],
    });

    console.log('Empacando .docx...');
    const buffer = await Packer.toBuffer(doc);
    fs.writeFileSync(outPath, buffer);
    console.log(`✅ Generado: ${outPath}`);
    console.log(`   Tamaño: ${(buffer.length / 1024).toFixed(1)} KB`);
}

build().catch(err => {
    console.error('❌ Error:', err);
    process.exit(1);
});
