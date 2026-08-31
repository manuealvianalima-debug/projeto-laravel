<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('admin.usuarios', compact('users'));
    }

    public function show(User $user)
    {
        return view('admin.usuarios.show', compact('user'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'Você não pode excluir seu próprio usuário!');
        }

        $nome = $user->name ?? $user->nome ?? 'Usuário';

        ActivityLogger::log(
            ActivityLog::ACTION_USER_DELETED,
            "Usuário excluído: {$nome} ({$user->email})",
            null,
            auth()->id(),
        );

        $user->delete();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }

    public function toggleAdmin(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'Você não pode alterar seu próprio cargo!');
        }

        if ($user->email === 'manuela.viana@fiocruz.br') {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'Este usuário possui permissão fixa de administrador.');
        }

        $user->is_admin = ! $user->is_admin;
        $user->save();

        $nome = $user->name ?? $user->nome ?? 'Usuário';
        $message = $user->is_admin
            ? "{$nome} agora é administrador!"
            : "{$nome} não é mais administrador!";

        ActivityLogger::log(
            ActivityLog::ACTION_ADMIN_TOGGLE,
            $message,
            $user,
            auth()->id(),
        );

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', $message);
    }

    // NOVO MÉTODO - ATIVIDADES DO USUÁRIO
    public function atividades(User $user)
    {
        // Verifica se a tabela existe
        if (! Schema::hasTable('activity_logs')) {
            return view('admin.usuarios', [
                'user' => $user,
                'activities' => collect(),
                'migrationPending' => true,
            ]);
        }

        // Busca as atividades do usuário
        $activities = ActivityLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('admin.usuarios', [
            'user' => $user,
            'activities' => $activities,
            'migrationPending' => false,
        ]);
    }
}