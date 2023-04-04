<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class DocumentAttributesNameRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $attribute = preg_replace('/attribute./', '', $attribute);
        if (!preg_match('/^[a-zA-Z0-9]{1,10}+$/', $attribute) || empty($attribute)) {
            $fail(__('messages.exception.400'));
        }
    }
}
