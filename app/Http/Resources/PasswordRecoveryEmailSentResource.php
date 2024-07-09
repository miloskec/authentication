<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PasswordRecoveryEmailSentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'status' => 'success',
            'message' => 'Password recovery email is sent.',
        ];
    }

    public function withoutDataWrapper()
    {
        return $this->resolve();
    }
}
