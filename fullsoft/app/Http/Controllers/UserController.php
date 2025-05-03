<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('gestionar_usuarios', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'title' => 'nullable|string|max:255',
            'commission' => 'nullable|numeric|min:0',
            'isAdmin' => 'nullable|boolean',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'title' => $validated['title'] ?? null,
            'commission' => $validated['commission'] ?? 0,
            'isAdmin' => $request->input('isAdmin', 0),
        ]);

        return redirect()->route('gestionar_usuarios')->with('success', 'Usuario creado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,{$id}",
            'password' => 'nullable|string|min:8',
            'title' => 'nullable|string|max:255',
            'commission' => 'nullable|numeric|min:0',
            'isAdmin' => 'nullable|boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'title' => $validated['title'] ?? $usuario->title,
            'commission' => $validated['commission'] ?? $usuario->commission,
            'isAdmin' => $request->input('isAdmin', 0),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $usuario->update($updateData);

        return redirect()->route('gestionar_usuarios')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete();
        return redirect()->route('gestionar_usuarios')->with('success', 'Usuario eliminado exitosamente.');
    }
}
