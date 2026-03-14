<?php

namespace Tests\Feature\Api;

use App\Enums\TaskStatus;
use App\Models\Bot;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BotTaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_bot_only_receives_tasks_assigned_to_it(): void
    {
        $owner = \App\Models\User::factory()->create();
        $bot = Bot::factory()->for($owner, 'owner')->create();
        $otherBot = Bot::factory()->create();
        $assignedTask = Task::factory()->assignedToBot($bot)->create();
        Task::factory()->assignedToBot($otherBot)->create();

        Sanctum::actingAs($bot, ['*']);

        $response = $this->getJson(route('api.tasks.index'));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $assignedTask->id);
    }

    public function test_bot_can_view_an_assigned_task(): void
    {
        $owner = \App\Models\User::factory()->create();
        $bot = Bot::factory()->for($owner, 'owner')->create();
        $task = Task::factory()->assignedToBot($bot)->create();

        Sanctum::actingAs($bot, ['*']);

        $response = $this->getJson(route('api.tasks.show', $task));

        $response->assertOk();
        $response->assertJsonPath('data.id', $task->id);
        $response->assertJsonPath('data.assignee.id', $bot->id);
        $response->assertJsonPath('data.assignee.type', 'bot');
    }

    public function test_bot_cannot_close_parent_task_with_open_subtasks(): void
    {
        $owner = \App\Models\User::factory()->create();
        $bot = Bot::factory()->for($owner, 'owner')->create();
        $parentTask = Task::factory()->assignedToBot($bot)->create();
        Task::factory()->for($parentTask, 'parentTask')->create([
            'status' => TaskStatus::InProgress,
        ]);

        Sanctum::actingAs($bot, ['*']);

        $response = $this->patchJson(route('api.tasks.status.update', $parentTask), [
            'status' => TaskStatus::Done->value,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['status']);
    }

    public function test_bot_can_add_a_delivery_comment(): void
    {
        $owner = \App\Models\User::factory()->create();
        $bot = Bot::factory()->for($owner, 'owner')->create();
        $task = Task::factory()->assignedToBot($bot)->create();

        Sanctum::actingAs($bot, ['*']);

        $response = $this->postJson(route('api.tasks.comments.store', $task), [
            'body' => 'Completed and ready for review.',
            'is_delivery' => true,
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.is_delivery', true);

        $this->assertDatabaseHas('task_comments', [
            'task_id' => $task->id,
            'author_type' => Bot::class,
            'author_id' => $bot->id,
            'is_delivery' => true,
        ]);
    }

    public function test_bot_can_create_a_subtask_for_another_bot(): void
    {
        $owner = \App\Models\User::factory()->create();
        $bot = Bot::factory()->for($owner, 'owner')->create();
        $delegateBot = Bot::factory()->for($owner, 'owner')->create();
        $task = Task::factory()->assignedToBot($bot)->create();

        Sanctum::actingAs($bot, ['*']);

        $response = $this->postJson(route('api.tasks.subtasks.store', $task), [
            'title' => 'Review generated output',
            'description' => 'Check whether the result matches the brief.',
            'priority' => 'high',
            'assignee_type' => 'bot',
            'assignee_id' => $delegateBot->id,
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.parent_task_id', $task->id);
        $response->assertJsonPath('data.assignee.id', $delegateBot->id);
        $response->assertJsonPath('data.creator.id', $bot->id);
    }

    public function test_bot_cannot_create_subtask_for_a_bot_in_another_tenant(): void
    {
        $owner = \App\Models\User::factory()->create();
        $bot = Bot::factory()->for($owner, 'owner')->create();
        $otherTenantBot = Bot::factory()->create();
        $task = Task::factory()->assignedToBot($bot)->create();

        Sanctum::actingAs($bot, ['*']);

        $response = $this->postJson(route('api.tasks.subtasks.store', $task), [
            'title' => 'Cross tenant delegation',
            'priority' => 'high',
            'assignee_type' => 'bot',
            'assignee_id' => $otherTenantBot->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['assignee_id']);
    }
}
