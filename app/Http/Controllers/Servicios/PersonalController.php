<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PersonalController extends Controller
{
    public function index(Request $request)
    {
        $personal = User::role('servicios_escolares')
            ->when($request->buscar, fn($q) =>
                $q->where('name', 'like', "%{$request->buscar}%")
                  ->orWhere('email', 'like', "%{$request->buscar}%")
            )
            ->orderBy('name')
            ->paginate(20)->withQueryString();

        return view('servicios.personal.index', compact('personal'));
    }

    public function create()
    {
        return view('servicios.personal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'email'     => 'required|email|unique:users,email',
        ], [
            'nombre.regex'    => 'El nombre solo debe contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo deben contener letras y espacios.',
        ]);

        $user = User::create([
            'name'     => "{$request->nombre} {$request->apellidos}",
            'email'    => $request->email,
            'password' => bcrypt('servicios' . date('Y')),
            'activo'   => true,
        ]);
        $user->assignRole('servicios_escolares');

        return redirect()->route('servicios.personal.index')->with('success', 'Personal registrado.');
    }

    public function show(User $personal)
    {
        return view('servicios.personal.show', compact('personal'));
    }

    public function edit(User $personal)
    {
        return view('servicios.personal.edit', compact('personal'));
    }

    public function update(Request $request, User $personal)
    {
        $request->validate([
            'nombre'    => ['required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u'],
            'apellidos' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'email'     => 'required|email|unique:users,email,' . $personal->id,
        ], [
            'nombre.regex'    => 'El nombre solo debe contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo deben contener letras y espacios.',
        ]);

        $personal->update([
            'name'  => "{$request->nombre} {$request->apellidos}",
            'email' => $request->email,
        ]);

        return redirect()->route('servicios.personal.index')->with('success', 'Personal actualizado.');
    }

    public function destroy(User $personal)
    {
        $personal->update(['activo' => false]);
        return redirect()->route('servicios.personal.index')->with('success', 'Personal desactivado.');
    }
}
