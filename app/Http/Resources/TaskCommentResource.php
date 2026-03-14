<?php

namespace App\Http\Resources;

use App\Models\Bot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskCommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'is_delivery' => $this->is_delivery,
            'author' => $this->author ? [
                'type' => $this->author instanceof Bot ? 'bot' : 'user',
                'id' => $this->author->getKey(),
                'name' => $this->actorName($this->author),
            ] : null,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    private function actorName(User|Bot $actor): string
    {
        if ($actor instanceof Bot) {
            return $actor->displayName();
        }

        return $actor->name;
    }
}
