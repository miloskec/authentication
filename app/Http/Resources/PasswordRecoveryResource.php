<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PasswordRecoveryResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->resource,
        ];
    }

    protected function message()
    {
        return 'Recovery token successfully generated.';
    }
}
