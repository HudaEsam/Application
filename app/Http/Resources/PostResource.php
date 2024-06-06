<?php

namespace App\Http\Resources;
use App\Models\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'Title' => $this->title,
            'Content' => $this->content,
            'Blogger' => new UserResource($this->user),
            'Category' => new CategoryResource($this->category),
            'created at' => $this->created_at->format('Y-m-d H:i:s'),

        ];
    }
}
