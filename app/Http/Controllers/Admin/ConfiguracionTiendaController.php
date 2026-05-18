<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionInstitucional;
use Illuminate\Http\Request;

/**
 * Configuracion de la tienda institucional:
 * cuenta bancaria, ubicacion de entrega, horario e instrucciones de pago.
 *
 * Solo el Administrador puede modificar estos valores. Los gestores
 * y alumnos los consumen via ConfiguracionInstitucional::get(...).
 */
class ConfiguracionTiendaController extends Controller
{
    public function edit()
    {
        $config = [
            'banco'         => ConfiguracionInstitucional::get('tienda.cuenta_banco'),
            'titular'       => ConfiguracionInstitucional::get('tienda.cuenta_titular'),
            'numero'        => ConfiguracionInstitucional::get('tienda.cuenta_numero'),
            'clabe'         => ConfiguracionInstitucional::get('tienda.cuenta_clabe'),
            'referencia'    => ConfiguracionInstitucional::get('tienda.referencia_prefijo'),
            'ubicacion'     => ConfiguracionInstitucional::get('tienda.ubicacion_entrega'),
            'horario'       => ConfiguracionInstitucional::get('tienda.horario_entrega'),
            'instrucciones' => ConfiguracionInstitucional::get('tienda.instrucciones_pago'),
        ];

        return view('admin.configuracion.tienda', compact('config'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'banco'         => 'required|string|max:80',
            'titular'       => 'required|string|max:150',
            'numero'        => ['required', 'string', 'max:30', 'regex:/^[0-9\s\-]+$/'],
            'clabe'         => ['required', 'string', 'size:18', 'regex:/^[0-9]+$/'],
            'referencia'    => 'nullable|string|max:20',
            'ubicacion'     => 'required|string|max:300',
            'horario'       => 'required|string|max:200',
            'instrucciones' => 'nullable|string|max:1000',
        ], [
            'numero.regex'  => 'El número de cuenta solo admite dígitos, espacios y guion.',
            'clabe.size'    => 'La CLABE debe tener exactamente 18 dígitos.',
            'clabe.regex'   => 'La CLABE solo admite dígitos.',
        ]);

        ConfiguracionInstitucional::set('tienda.cuenta_banco',        $data['banco'],         'Banco institucional', 'tienda');
        ConfiguracionInstitucional::set('tienda.cuenta_titular',      $data['titular'],       'Titular de la cuenta', 'tienda');
        ConfiguracionInstitucional::set('tienda.cuenta_numero',       $data['numero'],        'Numero de cuenta', 'tienda');
        ConfiguracionInstitucional::set('tienda.cuenta_clabe',        $data['clabe'],         'CLABE interbancaria', 'tienda');
        ConfiguracionInstitucional::set('tienda.referencia_prefijo', $data['referencia'] ?? '', 'Prefijo de referencia', 'tienda');
        ConfiguracionInstitucional::set('tienda.ubicacion_entrega',   $data['ubicacion'],     'Ubicacion fisica de entrega', 'tienda');
        ConfiguracionInstitucional::set('tienda.horario_entrega',     $data['horario'],       'Horario de atencion', 'tienda');
        ConfiguracionInstitucional::set('tienda.instrucciones_pago',  $data['instrucciones'] ?? '', 'Texto que vera el alumno', 'tienda');

        return redirect()->route('admin.configuracion.tienda')
            ->with('success', 'Configuración de la tienda actualizada.');
    }
}
