<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'client_id' => $this->client_id,
            'client' => $this->whenLoaded('client'), // Or ClientResource::make($this->whenLoaded('client'))
            'status' => $this->status, // Enum value or label if casted
            'active' => $this->active,
            'deadline' => $this->deadline?->format('Y-m-d'),
            'budget' => $this->budget,
            'user_id' => $this->user_id, // Project Manager / Creator
            'users' => $this->whenLoaded('users'), // Team members
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
