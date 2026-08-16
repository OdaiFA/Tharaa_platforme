<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'featured_image' => $this->featured_image ? url('storage/' . $this->featured_image) : null,
            'category' => $this->whenLoaded('category'),
            'author' => new UserResource($this->whenLoaded('author')),
            'views_count' => $this->views_count,
            'published_at' => $this->published_at,
        ];
    }
}
