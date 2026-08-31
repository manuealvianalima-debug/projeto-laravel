<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Tecnologia;
use App\Models\User;
use App\Rules\ReChaptcha;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $tecnologias = Tecnologia::with(['situacao', 'estagio'])
            ->orderByDesc('data_submissao')
            ->get();

        $stats = [
            'total_usuarios' => User::count(),
            'tecnologias' => Tecnologia::count(),
            'membro_desde' => $user->created_at->format('d/m/Y'),
            'ultimo_acesso' => ($user->ultimo_acesso ?? now())->format('d/m/Y H:i'),
        ];

        if ($user->isAdmin()) {
            $stats['usuarios_ativos'] = User::where('created_at', '>=', now()->subDays(30))->count();
            $stats['total_admins'] = User::where('is_admin', true)->count();
        }

        $tecnologiasExcluidas = Tecnologia::onlyTrashed()
            ->with(['situacao', 'responsavel'])
            ->orderByDesc('deleted_at')
            ->get();

        return view('dashboard', compact('user', 'stats', 'tecnologias', 'tecnologiasExcluidas'));
    }

    public function show()
    {
        return view('profile.show', ['user' => Auth::user()]);
    }

    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'descricao' => ['nullable', 'string', 'max:500'],
        ];

        if ($request->has('g-recaptcha-response')) {
            $rules['g-recaptcha-response'] = ['required', new ReChaptcha()];
        }

        $validated = $request->validate($rules);

        $antes = $user->only(['name', 'email', 'descricao']);
        $alterados = [];
        foreach (['name' => 'nome', 'email' => 'e-mail', 'descricao' => 'descrição'] as $campo => $rotulo) {
            if (array_key_exists($campo, $validated) && ($antes[$campo] ?? null) != $validated[$campo]) {
                $alterados[] = $rotulo;
            }
        }

        $user->update($validated);

        ActivityLogger::log(
            ActivityLog::ACTION_PROFILE_UPDATE,
            $alterados ? 'Perfil atualizado: '.implode(', ', $alterados) : 'Perfil atualizado',
            $user,
        );

        return redirect()->route('profile.show')->with('success', 'Perfil atualizado com sucesso!');
    }

    public function editPassword()
    {
        return view('profile.password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->update(['password' => Hash::make($validated['password'])]);

        ActivityLogger::log(ActivityLog::ACTION_PASSWORD_CHANGE, 'Senha alterada', $user);

        return redirect()->route('profile.show')->with('success', 'Senha alterada com sucesso!');
    }

    public function activity()
    {
        $user = Auth::user();

        $activities = Schema::hasTable('activity_logs')
            ? ActivityLog::where('user_id', $user->id)->orderByDesc('created_at')->paginate(20)
            : collect();

        return view('profile.activity', compact('user', 'activities'));
    }
}
