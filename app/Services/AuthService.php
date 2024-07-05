<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        if (!$token = JWTAuth::attempt($data)) {
            throw new AuthenticationException('Unauthorized', ['email']);
        }

        return auth()->user();
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return array(['message' => 'Successfully logged out']);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return array(['error' => 'Failed to logout, please try again.']);
        }
    }

    public function verify(string $token)
    {
        // Verify user
        return $token;
    }

    public function verifyJWT(string $token)
    {
        try {
            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();
            if (!$user) {
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
        return  $user;
    }

    public function getUserByIdAndVerifyJWTRequest(string $token, int $user_id)
    {
        try {
            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();
            if (!$user) {
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

        if (!$user) {
            throw new ModelNotFoundException('User not found', 404);
        }
        // Get user by ID and verify JWT
        return $user;
    }

    public function passwordRecovery(string $email)
    {
        // Send password recovery email
        return $email;
    }

    public function resetPassword(string $token, string $password, string $current_password)
    {
        JWTAuth::setToken($token);
        $user = JWTAuth::authenticate();
        //generate code to check if $current_password is equal to the current user password in the database
        if(Hash::check($current_password, $user->password_hash)) {
            $user->password_hash = Hash::make($password);
            $user->save();
            // Reset user password
            return $user;    
        }
        throw new AuthenticationException('Wrong current password');
    }

    public function refreshJWT(string $token)
    {
        JWTAuth::setToken($token);
        $user = JWTAuth::authenticate();
        // Refresh JWT
        return $user;
    }
}
