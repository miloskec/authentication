<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class ValidPasswordResetToken implements ValidationRule
{
    protected $email;

    /**
     * Create a new rule instance.
     *
     * @param  string  $email
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $passwordReset = DB::table('password_resets')
            ->where('email', $this->email)
            ->where('token', $value)
            ->first();

        if (! $passwordReset) {
            $fail('The provided password reset token is invalid.');

            return;
        }

        $tokenExpiresAt = Carbon::parse($passwordReset->created_at)
            ->addMinutes(config('auth.passwords.users.expire'));

        if (Carbon::now()->greaterThan($tokenExpiresAt)) {
            $fail('The provided password reset token has expired.');
        }
    }
}
