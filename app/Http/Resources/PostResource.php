<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\UserResource;
use App\Http\Resources\CommunityResource;

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
            'title' => $this->title,
            'content' => $this->content,
            'owner' => new UserResource($this->user),
            'community' => new CommunityResource($this->community),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
