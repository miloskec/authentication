<?php

namespace App\Services;

use App\Mail\PasswordReset;
use App\Models\User;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function register(array $data): User
    {
        // Register user
        $user = [
            'username' => $data['username'],
            'email' => $data['email'],
            'full_name' => $data['full_name'],
            'password_hash' => Hash::make($data['password']),
        ];

        if (isset($data['is_admin'])) {
            $user['is_admin'] = $data['is_admin'];
        }

        $user = User::create($user);

        // Publish user_created event to Kafka - through the notification

        return $user;
    }

    public function login(array $data): User
    {
        $keys = ['email', 'password'];
        $data = array_intersect_key($data, array_flip($keys));

        // Attempt to verify the credentials and create a token for the user
        if (! JWTAuth::attempt($data)) {
            throw new AuthenticationException('Unauthorized', ['email']);
        }

        return auth()->user();
    }

    public function logout(): array
    {
        try {
            JWTAuth::getToken();
            JWTAuth::invalidate(true);

            return ['message' => 'Successfully logged out'];
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return ['error' => 'Failed to logout, please try again.'];
        }
    }

    public function verify(string $token)
    {
        // Verify user
        return $token;
    }

    public function verifyJWT(string $token): User
    {
        try {
            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();
            if (! $user instanceof User) {
                throw new AuthenticationException('User not authenticated');
            }
        } catch (Exception $e) {
            Log::channel('authentication')->info(
                sprintf(
                    'Error: %s in %s on line %d',
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                )
            );
            throw $e;
        }

        return $user;
    }

    public function getUserByIdAndVerifyJWTRequest(string $token, int $user_id): User
    {
        try {
            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();
            if (! $user) {
                throw new AuthenticationException('Unauthorized', ['email']);
            }
        } catch (Exception $e) {
            Log::channel('authentication')->info(
                sprintf(
                    'Error: %s in %s on line %d',
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                )
            );
            throw $e;
        }
        $user = User::find($user_id);

        if (! $user) {
            throw new ModelNotFoundException('User not found', 404);
        }

        // Get user by ID and verify JWT
        return $user;
    }

    public function passwordRecovery(string $email): string
    {
        // Send password recovery email
        $user = User::where('email', $email)->first();

        return $user->createPasswordResetToken();
    }

    public function sendPasswordRecoveryEmail(string $email): void
    {
        // Send password recovery email
        $user = User::where('email', $email)->first();
        $token = $user->createPasswordResetToken();

        Mail::to($email)->send(new PasswordReset($token));
    }

    public function resetPassword(string $token, string $password, string $current_password): User
    {
        JWTAuth::setToken($token);
        $user = JWTAuth::authenticate();
        if (! $user instanceof User) {
            throw new AuthenticationException('User not authenticated');
        }
        //generate code to check if $current_password is equal to the current user password in the database
        if (Hash::check($current_password, $user->password_hash)) {
            $user->password_hash = Hash::make($password);
            $user->save();

            // Reset user password
            return $user;
        }
        throw new AuthenticationException('Wrong current password');
    }

    public function resetPasswordWithToken(string $email, string $password): bool
    {
        // Reset password with token
        $user = User::where('email', $email)->first();
        $user->password_hash = Hash::make($password);
        $user->save();
        // Delete the password reset token
        DB::table('password_resets')->where('email', $email)->delete();

        return true;
    }

    public function refreshJWT(string $token): User
    {
        try {
            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();
            if (! $user instanceof User) {
                throw new AuthenticationException('User not authenticated');
            }
        } catch (Exception $e) {
            Log::channel('authentication')->info(
                sprintf(
                    'Error: %s in %s on line %d',
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                )
            );
            throw $e;
        }

        // Refresh JWT
        return $user;
    }
}
