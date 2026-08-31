<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Rules\ReChaptcha;
use App\Support\ActivityLogger;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            //'g-recaptcha-response' => ['required', new ReChaptcha()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        ActivityLogger::log(ActivityLog::ACTION_ACCOUNT_CREATED, 'Conta criada', $user, $user->id);


        Auth::login($user);

        ActivityLogger::log(ActivityLog::ACTION_LOGIN, 'Login realizado', $user);
        return redirect('/user');
    }
}
