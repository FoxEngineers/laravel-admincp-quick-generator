<?php

namespace FoxEngineers\AdminCP\Rules\Auth;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ChangePassword.
 */
class ChangePassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $uppercase = preg_match('@[A-Z]@', $value);
        $lowercase = preg_match('@[a-z]@', $value);
        $number = preg_match('@\d@', $value);

        $status = $uppercase && $lowercase && $number && \strlen($value) >= 8;

        if (!$status) {
            $fail(__('auth.password_rules'));
        }
    }
}
