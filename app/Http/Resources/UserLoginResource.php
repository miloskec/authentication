<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @property int $id
 * @property string $email
 * @property string $username
 * @property string $full_name
 */
class UserLoginResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->id,
                'email' => $this->email,
                'username' => $this->username,
                'full_name' => $this->full_name,
            ],
            'token' => $this->getToken(),
        ];
    }

    protected function getToken(): array
    {
        try {
            $decodedToken = [];
            $token = JWTAuth::fromUser($this->resource);
            $decodedToken = JWTAuth::setToken($token)->getPayload()->toArray();
            $expirationDate = Carbon::createFromTimestamp($decodedToken['exp']);
            $issuedAt = Carbon::createFromTimestamp($decodedToken['iat']);

            return [
                'access_token' => $token,
                'expires_at' => $expirationDate->toDateTimeString(),
                'issued_at' => $issuedAt->toDateTimeString(),
            ];
        } catch (\Exception $e) {
            // Handle parsing errors, such as if the token is invalid or expired
            return [
                'error' => 'Failed to parse token: '.$e->getMessage().json_encode($decodedToken),
            ];
        }
    }

    protected function message()
    {
        return 'User data retrieved successfully.';
    }
}
