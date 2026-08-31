<?php
namespace  App\Http\Controllers;

use  Aacotroneo\Saml2\Events\Saml2LoginEvent;
use  Aacotroneo\Saml2\Http\Controllers\Saml2Controller;
use  Aacotroneo\Saml2\Saml2Auth;
use  Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class  LoginController  extends  Saml2Controller
{
	public function index()
    {
        // Retorna a view de login
        return view('login');
    }

   public  function  login(Saml2Auth $saml2Auth)
   {
   	$loginRoute =  session()->pull('url.intended', config('saml2_settings.loginRoute'));

   	// If user isn't authenticated, auth them then redirect to where they wanted to go, else just redirect
   	$saml2Auth->login($loginRoute);
	return  auth()->guest()
   		? $saml2Auth->login($loginRoute)
   		:  redirect()->intended();


   }

   public  function  acs(Saml2Auth $saml2Auth, $idpName)
   {
   	$errors = $saml2Auth->acs();
	
   	if (!empty($errors)) {
   		logger()->error('Saml2 error_detail', ['error' => $saml2Auth->getLastErrorReason()]);
   		session()->flash('saml2_error_detail', [$saml2Auth->getLastErrorReason()]);
   		logger()->error('Saml2 error', $errors);
   		session()->flash('saml2_error', $errors);
   		return  redirect(config('saml2_settings.errorRoute'));
   	}
    
   	$user = $saml2Auth->getSaml2User();

   	# chama Listeners/LoginListener
   	event(new Saml2LoginEvent($idpName, $user, $saml2Auth));
     

	$permissao = session('permissao');
  	switch ($permissao) {

		case 'root':
			return redirect()->route('dashboard_adm');
		case 'avaliador':
			return redirect()->route('dashboard');
		case 'auditor': 
			return redirect()->route('dashboard_auditor');
		default:
			return redirect()->route('home'); 
	}


   	# retorna para a url que tentou acessar ou a padrão
   	// $redirectUrl = !empty($user->getIntendedUrl())
   	// 	? $user->getIntendedUrl()
   	// 	:  config('saml2_settings.loginRoute');

	// 	dd($redirectUrl);

   	// return  redirect($redirectUrl);
   }

   public  function  logout(Saml2Auth $saml2Auth, Request $request)
   {
   	auth()->logout();

   	$returnTo =  config('saml2_settings.logoutRoute');
   	$sessionIndex =  session()->get('sessionIndex');
   	$nameId =  session()->get('nameId');

   	$request->session()->invalidate();
   	$request->session()->regenerateToken();

   	return $saml2Auth->logout($returnTo, $nameId, $sessionIndex);
   }
}
