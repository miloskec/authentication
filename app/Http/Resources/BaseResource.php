<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    /**
     * Add data to the resource response.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function with($request)
    {
        return [
            'status' => 'success',
            'message' => $this->message()
        ];
    }

    /**
     * Provides a default message for the resource.
     *
     * @return string
     */
    protected function message()
    {
        return '';
    }
}
