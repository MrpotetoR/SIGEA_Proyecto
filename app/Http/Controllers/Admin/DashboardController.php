<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\PersonalServiciosEscolares;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPersonal       = PersonalServiciosEscolares::count();
        $totalAdmins         = User::role('admin')->count();
        $totalCarreras       = Carrera::count();
        $carrerasAsignadas   = Carrera::has('personalAsignado')->count();
        $carrerasSinAsignar  = $totalCarreras - $carrerasAsignadas;

        $sinAsignar = Carrera::doesntHave('personalAsignado')
            ->orderBy('nombre_carrera')
            ->take(10)
            ->get();

        $personalReciente = PersonalServiciosEscolares::with('user', 'carreras')
            ->latest('id_personal')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPersonal', 'totalAdmins', 'totalCarreras',
            'carrerasAsignadas', 'carrerasSinAsignar',
            'sinAsignar', 'personalReciente'
        ));
    }
}
