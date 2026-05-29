<?php

namespace App\Rules;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TurnstileToken implements ValidationRule
{
    public function __construct(
        private ?string $ipAddress = null
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (app(TurnstileService::class)->verify((string) $value, $this->ipAddress)) {
            return;
        }

        $fail('Nie udało się zweryfikować zabezpieczenia. Odśwież formularz i spróbuj ponownie.');
    }
}
