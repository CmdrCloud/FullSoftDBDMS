<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
            'email' => 'required|string|email|max:255i|unique:users',
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

    /**
     * Reset a user's commission to zero (after payout)
     */
    public function resetCommission($id)
    {
        // Only allow admins to reset others' commissions
        if (Auth::user()->isAdmin || Auth::id() == $id) {
            $user = User::findOrFail($id);
            $oldCommission = $user->commission;
            $user->commission = 0;
            $user->save();

            return response()->json([
                'message' => 'Comisión restablecida exitosamente',
                'previous_commission' => $oldCommission,
                'current_commission' => 0
            ]);
        }

        return response()->json(['error' => 'No autorizado'], 403);
    }

    /**
     * View all user commissions (admin only)
     */
    public function viewCommissions()
    {
        if (!Auth::user()->isAdmin) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $users = User::select('id', 'name', 'email', 'commission')->get();
        return response()->json($users);
    }

    /**
     * View current user's commission
     */
    public function myCommission()
    {
        $user = Auth::user();
        return response()->json([
            'commission' => $user->commission,
            'name' => $user->name
        ]);
    }
}
