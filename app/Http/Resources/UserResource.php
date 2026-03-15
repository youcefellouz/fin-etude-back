<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{ //tt7akem fl response
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role, 
            'created_date' => $this->created_at->format('d-m-Y'),
            'profile' => new ProfileResource($this->whenLoaded('profile')),
        ];
    }
}
