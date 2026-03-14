<?php

namespace App\Http\Resources;

use App\Models\Bot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_task_id' => $this->parent_task_id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
            'priority' => $this->priority->value,
            'requested_start_at' => $this->requested_start_at?->toISOString(),
            'closed_at' => $this->closed_at?->toISOString(),
            'can_be_closed' => $this->canBeClosed(),
            'has_open_subtasks' => $this->hasOpenSubtasks(),
            'creator' => $this->creator ? [
                'type' => $this->creator instanceof Bot ? 'bot' : 'user',
                'id' => $this->creator->getKey(),
                'name' => $this->actorName($this->creator),
            ] : null,
            'assignee' => $this->assignee ? [
                'type' => $this->assignee instanceof Bot ? 'bot' : 'user',
                'id' => $this->assignee->getKey(),
                'name' => $this->actorName($this->assignee),
            ] : null,
            'subtasks' => self::collection($this->whenLoaded('subtasks')),
            'comments' => TaskCommentResource::collection($this->whenLoaded('comments')),
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
