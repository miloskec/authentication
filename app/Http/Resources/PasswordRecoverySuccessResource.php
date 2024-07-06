<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PasswordRecoverySuccessResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'status' => 'success',
            'message' => 'Password recovery is successful.'
        ];
    }

    public function withoutDataWrapper()
    {
        return $this->resolve();
    }
}
