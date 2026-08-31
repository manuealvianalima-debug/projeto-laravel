<?php

namespace App\Listeners;

use MrBrownNL\Saml2\Events\Saml2LoginEvent;
use App\Models\User; // ou App\User dependendo da versão
use Illuminate\Support\Facades\Auth;

class LoginListener
{
    public function handle(Saml2LoginEvent $event)
    {
        $samlUser = $event->getSaml2User();

        // Pega atributos vindos do SSO
        $attributes = $samlUser->getAttributes();

        // LOG para debug
        \Log::info('SAML2 Login - Atributos recebidos:', $attributes);

        //  ajuste os nomes conforme o retorno da Fiocruz
        // Conforme config/saml2/test_idp_settings.php attributes_organization
        $email = $attributes['email'][0] ?? null;
        $nome  = ($attributes['firstName'][0] ?? '') . ' ' . ($attributes['lastName'][0] ?? '');

        \Log::info('SAML2 Login - Email extraído:', ['email' => $email, 'nome' => $nome]);

        if (!$email) {
            \Log::error('SAML2 Login - Email não retornado pelo SSO');
            abort(403, 'Email não retornado pelo SSO');
        }

        //  procura usuário
        $user = User::where('email', $email)->first();

        //  cria se não existir
        if (!$user) {
            \Log::info('SAML2 Login - Criando novo usuário:', ['email' => $email, 'nome' => $nome]);
            $user = User::create([
                'name' => $nome,
                'email' => $email,
                'password' => bcrypt(str()->random(16)),
            ]);
        } else {
            \Log::info('SAML2 Login - Usuário encontrado:', ['email' => $email, 'user_id' => $user->id]);
        }

        //  LOGIN NO LARAVEL
        Auth::login($user);
        \Log::info('SAML2 Login - Usuário autenticado com sucesso:', ['email' => $email]);

        //  redireciona para dashboard
        return redirect('/dashboard');
    }
}
