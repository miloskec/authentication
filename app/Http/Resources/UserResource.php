<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * @property int $id
 * @property string $email
 * @property string $username
 * @property string $full_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class UserResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'full_name' => $this->full_name,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }

    protected function message()
    {
        return 'User data retrieved successfully.';
    }
}
