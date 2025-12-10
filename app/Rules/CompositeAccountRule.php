<?php

namespace App\Rules;

use App\Models\AccountModel;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CompositeAccountRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $account = AccountModel::find($value);

        if (!$account) {
            $fail("الحساب غير موجود");
            return;
        }

        if ($account->type !== 'composite') {
            $fail("الحساب يجب أن يكون من نوع composite وليس " . $account->type);
        }
    }
}
