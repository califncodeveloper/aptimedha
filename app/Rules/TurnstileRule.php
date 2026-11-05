<?php

namespace App\Rules;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates a Cloudflare Turnstile CAPTCHA response token.
 */
class TurnstileRule implements ValidationRule
{
    public function __construct(
        protected ?string $action = null
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || !is_string($value)) {
            $fail('Please complete the Cloudflare security verification.');
            return;
        }

        $service = app(TurnstileService::class);
        $remoteIp = request()->ip();

        if (!$service->verify($value, $remoteIp, $this->action)) {
            $fail('Cloudflare security verification failed. Please try again.');
        }
    }
}

