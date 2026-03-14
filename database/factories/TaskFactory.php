<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Bot;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_task_id' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => TaskStatus::Open,
            'priority' => fake()->randomElement(TaskPriority::cases()),
            'requested_start_at' => fake()->optional()->dateTimeBetween('now', '+1 week'),
            'closed_at' => null,
            'creator_type' => User::class,
            'creator_id' => User::factory(),
            'assignee_type' => User::class,
            'assignee_id' => User::factory(),
        ];
    }

    public function assignedToBot(?Bot $bot = null): static
    {
        return $this->state(fn () => [
            'assignee_type' => Bot::class,
            'assignee_id' => $bot?->getKey() ?? Bot::factory(),
        ]);
    }

    public function createdByBot(?Bot $bot = null): static
    {
        return $this->state(fn () => [
            'creator_type' => Bot::class,
            'creator_id' => $bot?->getKey() ?? Bot::factory(),
        ]);
    }

    public function asSubtask(?Task $parentTask = null): static
    {
        return $this->state(fn () => [
            'parent_task_id' => $parentTask?->getKey() ?? Task::factory(),
        ]);
    }
}
