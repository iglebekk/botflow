<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Models\Bot;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $assignee = $this->string('assignee')->value();

        if ($assignee === '' || $this->has('assignee_type') || $this->has('assignee_id')) {
            return;
        }

        [$assigneeType, $assigneeId] = array_pad(explode(':', $assignee, 2), 2, null);

        $this->merge([
            'assignee_type' => $assigneeType,
            'assignee_id' => $assigneeId,
        ]);
    }

    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        if ($this->route('task') instanceof Task) {
            return $user->can('createSubtask', $this->route('task'));
        }

        return $user->can('create', Task::class);
    }

    public function rules(): array
    {
        $tenantUser = $this->tenantUser();

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', Rule::enum(TaskPriority::class)],
            'requested_start_at' => ['nullable', 'date'],
            'assignee' => ['nullable', 'string'],
            'assignee_type' => ['required', Rule::in(['user', 'bot'])],
            'assignee_id' => [
                'required',
                'integer',
                match ($this->input('assignee_type')) {
                    'bot' => Rule::exists('bots', 'id')->where(fn ($query) => $query->where('user_id', $tenantUser->id)),
                    default => Rule::exists('users', 'id')->where(fn ($query) => $query->where('id', $tenantUser->id)),
                },
            ],
        ];
    }

    public function assignee(): User|Bot
    {
        $modelClass = $this->assigneeModelClass();

        /** @var User|Bot $model */
        $model = match ($modelClass) {
            Bot::class => Bot::query()
                ->where('user_id', $this->tenantUser()->id)
                ->findOrFail($this->integer('assignee_id')),
            default => User::query()
                ->whereKey($this->tenantUser()->id)
                ->findOrFail($this->integer('assignee_id')),
        };

        return $model;
    }

    /**
     * @return class-string<Model>
     */
    public function assigneeModelClass(): string
    {
        return match ($this->string('assignee_type')->value()) {
            'bot' => Bot::class,
            default => User::class,
        };
    }

    private function tenantUser(): User
    {
        /** @var User|Bot $actor */
        $actor = $this->user();

        if ($actor instanceof Bot) {
            return $actor->owner;
        }

        return $actor;
    }
}
