<?php

namespace App\Http\Requests\Concerns;

use App\Rules\TurnstileToken;
use App\Services\TurnstileService;

trait ValidatesTurnstile
{
    /**
     * @return array<int, \Illuminate\Contracts\Validation\ValidationRule|string>
     */
    protected function turnstileRules(): array
    {
        if (! app(TurnstileService::class)->enabled()) {
            return ['nullable'];
        }

        return [
            'required',
            'string',
            'max:2048',
            new TurnstileToken($this->ip()),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function turnstileMessages(): array
    {
        return [
            'turnstile_token.required' => 'Potwierdź, że nie jesteś automatem.',
            'turnstile_token.max' => 'Nie udało się zweryfikować zabezpieczenia. Odśwież formularz i spróbuj ponownie.',
        ];
    }
}
