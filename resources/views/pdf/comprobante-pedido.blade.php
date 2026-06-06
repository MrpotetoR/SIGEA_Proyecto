<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante {{ $pedido->folio }}</title>
    <style>
        @page { margin: 30px 40px; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #2c3e50; font-size: 11px; }
        .header { text-align: center; border-bottom: 3px solid #0606F0; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { color: #04276B; margin: 0 0 4px; font-size: 22px; }
        .header p { margin: 0; color: #888; font-size: 11px; }
        .folio-box { background: #f0f4ff; border: 1px solid #0606F0; padding: 14px; border-radius: 6px; margin-bottom: 22px; }
        .folio-box .label { font-size: 9px; color: #04276B; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; }
        .folio-box .folio { font-size: 20px; font-weight: bold; color: #0606F0; font-family: 'DejaVu Sans Mono', monospace; margin: 4px 0; }
        .meta { width: 100%; margin-bottom: 22px; }
        .meta td { padding: 4px 0; vertical-align: top; }
        .meta .lbl { color: #888; font-size: 10px; text-transform: uppercase; width: 25%; }
        .meta .val { font-weight: bold; color: #2c3e50; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
        table.items th { background: #04276B; color: white; padding: 8px; text-align: left; font-size: 10px; text-transform: uppercase; }
        table.items td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        table.items .num { text-align: right; }
        table.items .center { text-align: center; }
        .totales { width: 50%; margin-left: auto; }
        .totales td { padding: 4px 0; }
        .totales .total-row td { border-top: 2px solid #04276B; padding-top: 8px; font-size: 14px; font-weight: bold; color: #0606F0; }
        .estado { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 10px; font-weight: bold; }
        .estado-aprobado { background: #dbeafe; color: #1e40af; }
        .estado-listo_recoger { background: #dcfce7; color: #166534; }
        .estado-entregado { background: #d1fae5; color: #065f46; }
        .footer { margin-top: 35px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 9px; color: #888; text-align: center; }
        .pickup { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; border-radius: 4px; margin-top: 20px; }
        .pickup .label { font-size: 9px; color: #92400e; text-transform: uppercase; font-weight: bold; }
        .pickup .text { color: #78350f; font-size: 11px; margin-top: 3px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>UDEA — Universidad de Los Ángeles</h1>
        <p>Comprobante de Pedido · Tienda Institucional</p>
    </div>

    <div class="folio-box">
        <div class="label">Folio del pedido</div>
        <div class="folio">{{ $pedido->folio }}</div>
        <span class="estado estado-{{ $pedido->estado }}">{{ $pedido->estado_label }}</span>
    </div>

    <table class="meta">
        <tr>
            <td class="lbl">Cliente:</td>
            <td class="val">{{ $pedido->usuario->name }}</td>
            <td class="lbl">Fecha de pedido:</td>
            <td class="val">{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="lbl">Correo:</td>
            <td>{{ $pedido->usuario->email }}</td>
            <td class="lbl">Pago validado:</td>
            <td>{{ $pedido->revisado_en?->format('d/m/Y H:i') ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">Nivel:</td>
            <td>{{ ucfirst($pedido->nivel_educativo) }}</td>
            <td class="lbl">Validado por:</td>
            <td>{{ $pedido->revisor?->name ?? '—' }}</td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th class="center">Cant.</th>
                <th class="num">Precio</th>
                <th class="num">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->items as $item)
                <tr>
                    <td style="font-family: monospace; font-size: 10px;">{{ $item->codigo_snapshot }}</td>
                    <td>{{ $item->descripcion }}</td>
                    <td class="center">{{ $item->cantidad }}</td>
                    <td class="num">${{ number_format($item->precio_unitario, 2) }}</td>
                    <td class="num"><strong>${{ number_format($item->subtotal, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totales">
        <tr class="total-row">
            <td>TOTAL</td>
            <td style="text-align: right;">${{ number_format($pedido->total, 2) }} MXN</td>
        </tr>
    </table>

    @if(in_array($pedido->estado, ['aprobado', 'listo_recoger'], true))
        <div class="pickup">
            <div class="label">📍 Punto de entrega</div>
            <div class="text">{{ $ubicacion }}</div>
            <div class="text"><strong>Horario:</strong> {{ $horario }}</div>
            <div class="text" style="margin-top:6px; font-style: italic;">Presenta este comprobante y una identificación oficial al recoger tu pedido.</div>
        </div>
    @endif

    <div class="footer">
        Documento generado electrónicamente por UDEA — {{ now()->format('d/m/Y H:i') }}.
        <br>
        Universidad de Los Ángeles · {{ config('app.name', 'UDEA') }} · {{ date('Y') }}
    </div>
</body>
</html>
