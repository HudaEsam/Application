<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class UserResource extends JsonResource
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
            'Blogger_name' => $this->name,
            'Blogger_email'=> $this->email,
            'created at' => $this->created_at->format('Y-m-d H:i:s'),

        ];

        // return parent::toArray($request);
    }
}
