<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this->resource['user']),
            'access_token' => $this->when(
                $this->resource['token'], 
                $this->resource['token']
            ),
            'token_type' => $this->resource['token_type'],
            'expires_in' => $this->when(
                $this->resource['expires_in'],
                $this->resource['expires_in']
            ),
        ];
    }
}