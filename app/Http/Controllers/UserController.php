<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\ReChaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        
        $users = User::with('unidade')
        ->orderBy('name')
        ->paginate(20);

    return view('admin.usuarios.index', compact('users'));

    }

    public function show(User $user)
    {
        return view('admin.usuarios.show', compact('user'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'g-recaptcha-response' => ['required', new ReChaptcha()],
        ]);

        $validated['password'] = bcrypt($validated['password']);

        User::create($validated);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    public function toggleAdmin(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Você não pode alterar sua própria permissão.');
        }

        $user->update([
            'is_admin' => ! $user->is_admin,
        ]);

        return back()->with('success', 'Permissão atualizada.');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Você não pode excluir seu próprio usuário.');
        }

        $user->delete();

        return back()->with('success', 'Usuário removido.');
    }
}