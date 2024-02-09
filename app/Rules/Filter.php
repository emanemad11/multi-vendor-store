<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Filter implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $forbiddenNames;

    public function __construct(array $forbiddenNames)
    {
        $this->forbiddenNames = $forbiddenNames;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (in_array(strtolower($value), $this->forbiddenNames)) {
            $fail("This value for $attribute is forbidden");
        }
    }
}
