<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('activity_logs')) {
            return view('admin.atividades.index', [
                'activities' => collect(),
                'users' => User::orderByRaw('COALESCE(name, nome, email)')
                    ->get(['id', 'name', 'nome', 'email']),
                'actionLabels' => ActivityLog::actionLabels(),
                'migrationPending' => true,
            ]);
        }

        $query = ActivityLog::with('user')->orderByDesc('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        $activities = $query->paginate(25)->withQueryString();

        $users = User::orderByRaw('COALESCE(name, nome, email)')
            ->get(['id', 'name', 'nome', 'email']);

        $actionLabels = ActivityLog::actionLabels();

        return view('admin.atividades.index', compact('activities', 'users', 'actionLabels'));
    }

    public function user(User $user)
    {
        if (!Schema::hasTable('activity_logs')) {
            return view('admin.atividades.user', [
                'user' => $user,
                'activities' => collect(),
                'migrationPending' => true,
            ]);
        }

        $activities = ActivityLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('admin.atividades.user', compact('user', 'activities'));
    }
}
