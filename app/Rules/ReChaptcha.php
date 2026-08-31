<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;

class ReChaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $token = (string) $value;

        if ($token === '') {
            $fail('Por favor, confirme que voce nao e um robo.');
            return;
        }

        $secretKey = config('services.recaptcha.secret_key');

        if (empty($secretKey)) {
            $fail('A chave secreta do reCAPTCHA nao foi configurada.');
            return;
        }

        $response = Http::asForm()
            ->timeout(10)
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

        $result = $response->json();

        if (! $response->ok() || ! ($result['success'] ?? false)) {
            $fail('A verificacao de seguranca falhou. Tente novamente.');
        }
    }
}
