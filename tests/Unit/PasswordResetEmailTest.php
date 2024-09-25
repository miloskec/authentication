<?php

namespace Tests\Unit;

use App\Mail\PasswordReset;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetEmailTest extends TestCase
{
    public function test_password_reset_email_is_sent()
    {
        $token = '123456789';
        Mail::to('example@example.com')->send(new PasswordReset($token));
        Mail::assertQueued(PasswordReset::class, function ($mail) use ($token) {
            // Check that the mailable uses the correct data
            return $mail->token === $token && $mail->hasTo('example@example.com');
        });
    }

    public function test_email_content()
    {
        $token = '123456789';
        $mailable = new PasswordReset($token);

        $rendered = $mailable->render();

        // Assertions to verify content of the email
        $this->assertStringContainsString($token, $rendered);
        $this->assertStringContainsString('Password Reset', $rendered);
    }
}
