<?php

namespace Database\Factories;

use App\Models\Bot;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskComment>
 */
class TaskCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'body' => fake()->paragraph(),
            'is_delivery' => false,
            'author_type' => User::class,
            'author_id' => User::factory(),
        ];
    }

    public function byBot(?Bot $bot = null): static
    {
        return $this->state(fn () => [
            'author_type' => Bot::class,
            'author_id' => $bot?->getKey() ?? Bot::factory(),
        ]);
    }

    public function asDelivery(): static
    {
        return $this->state(fn () => [
            'is_delivery' => true,
        ]);
    }
}
