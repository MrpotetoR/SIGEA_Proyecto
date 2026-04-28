<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdministradoresController extends Controller
{
    public function index(Request $request)
    {
        $admins = User::role('admin')
            ->when($request->buscar, fn($q) =>
                $q->where('name', 'like', "%{$request->buscar}%")
                  ->orWhere('email', 'like', "%{$request->buscar}%")
            )
            ->orderBy('name')
            ->paginate(20)->withQueryString();

        return view('admin.administradores.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.administradores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:150', 'regex:/^[\pL\s]+$/u'],
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.regex' => 'El nombre solo debe contener letras y espacios.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'activo'   => true,
        ]);
        $user->assignRole('admin');

        return redirect()->route('admin.administradores.index')
            ->with('success', 'Administrador creado correctamente.');
    }

    public function edit(User $administradore)
    {
        $admin = $administradore;
        if (!$admin->hasRole('admin')) {
            abort(404);
        }
        return view('admin.administradores.edit', compact('admin'));
    }

    public function update(Request $request, User $administradore)
    {
        $admin = $administradore;
        if (!$admin->hasRole('admin')) {
            abort(404);
        }

        $request->validate([
            'name'     => ['required', 'string', 'max:150', 'regex:/^[\pL\s]+$/u'],
            'email'    => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.regex' => 'El nombre solo debe contener letras y espacios.',
        ]);

        $admin->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $admin->update(['password' => bcrypt($request->password)]);
        }

        return redirect()->route('admin.administradores.index')
            ->with('success', 'Administrador actualizado.');
    }

    public function destroy(User $administradore)
    {
        $admin = $administradore;
        if (!$admin->hasRole('admin')) {
            abort(404);
        }

        // No permitir auto-eliminación.
        if ($admin->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        // Mantener al menos un admin activo en el sistema.
        $admins = User::role('admin')->where('activo', true)->count();
        if ($admins <= 1) {
            return back()->with('error', 'No se puede eliminar al último administrador del sistema.');
        }

        $admin->update(['activo' => false]);
        $admin->delete(); // soft delete

        return redirect()->route('admin.administradores.index')
            ->with('success', 'Administrador eliminado.');
    }
}
