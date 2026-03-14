<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Bot;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_task_index(): void
    {
        $this->get(route('tasks.index'))
            ->assertRedirect(route('login'));
    }

    public function test_dashboard_displays_tasks_grouped_by_status(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Task::factory()->create([
            'title' => 'Open task',
            'creator_id' => $user->id,
            'status' => TaskStatus::Open,
        ]);
        Task::factory()->create([
            'title' => 'Blocked task',
            'creator_id' => $user->id,
            'status' => TaskStatus::Blocked,
        ]);
        Task::factory()->create([
            'title' => 'Done task',
            'creator_id' => $user->id,
            'status' => TaskStatus::Done,
            'closed_at' => now(),
        ]);
        Task::factory()->create([
            'title' => 'Other tenant task',
            'creator_id' => $otherUser->id,
            'assignee_id' => $otherUser->id,
            'status' => TaskStatus::Open,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeText(__('tasks.dashboard.title'));
        $response->assertSeeText('Open task');
        $response->assertSeeText('Blocked task');
        $response->assertSeeText('Done task');
        $response->assertDontSeeText('Other tenant task');
        $response->assertSeeText(__('tasks.status.open'));
        $response->assertSeeText(__('tasks.status.blocked'));
        $response->assertSeeText(__('tasks.status.done'));
    }

    public function test_authenticated_user_can_create_a_task_for_a_bot(): void
    {
        $user = User::factory()->create();
        $bot = Bot::factory()->for($user, 'owner')->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Prepare API summary',
            'description' => 'Summarize the current implementation.',
            'priority' => 'high',
            'assignee' => "bot:{$bot->id}",
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'title' => 'Prepare API summary',
            'creator_type' => User::class,
            'creator_id' => $user->id,
            'assignee_type' => Bot::class,
            'assignee_id' => $bot->id,
        ]);
    }

    public function test_authenticated_user_can_add_comment_and_subtask(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'creator_id' => $user->id,
            'assignee_id' => $user->id,
        ]);
        $delegateBot = Bot::factory()->for($user, 'owner')->create();

        $this->actingAs($user)->post(route('tasks.comments.store', $task), [
            'body' => 'Please prioritize this today.',
            'is_delivery' => false,
        ])->assertRedirect(route('tasks.show', $task));

        $this->actingAs($user)->post(route('tasks.subtasks.store', $task), [
            'title' => 'Review the output',
            'description' => 'Check structure and completeness.',
            'priority' => 'normal',
            'assignee' => "bot:{$delegateBot->id}",
        ])->assertRedirect(route('tasks.show', $task));

        $this->assertDatabaseHas('task_comments', [
            'task_id' => $task->id,
            'body' => 'Please prioritize this today.',
        ]);

        $this->assertDatabaseHas('tasks', [
            'parent_task_id' => $task->id,
            'title' => 'Review the output',
            'assignee_type' => Bot::class,
            'assignee_id' => $delegateBot->id,
        ]);
    }

    public function test_parent_task_cannot_be_closed_from_web_when_subtasks_are_open(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'creator_id' => $user->id,
            'assignee_id' => $user->id,
        ]);
        Task::factory()->for($task, 'parentTask')->create([
            'creator_id' => $user->id,
            'assignee_id' => $user->id,
            'status' => TaskStatus::InProgress,
        ]);

        $response = $this->actingAs($user)->from(route('tasks.show', $task))
            ->patch(route('tasks.status.update', $task), [
                'status' => TaskStatus::Done->value,
            ]);

        $response->assertRedirect(route('tasks.show', $task));
        $response->assertSessionHasErrors('status');
    }

    public function test_user_cannot_view_another_users_task(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'creator_id' => $otherUser->id,
            'assignee_id' => $otherUser->id,
        ]);

        $this->actingAs($user)
            ->get(route('tasks.show', $task))
            ->assertForbidden();
    }

    public function test_user_cannot_assign_task_to_another_users_bot(): void
    {
        $user = User::factory()->create();
        $otherUsersBot = Bot::factory()->create();

        $response = $this->actingAs($user)->from(route('tasks.create'))
            ->post(route('tasks.store'), [
                'title' => 'Cross-tenant task',
                'priority' => 'normal',
                'assignee' => "bot:{$otherUsersBot->id}",
            ]);

        $response->assertRedirect(route('tasks.create'));
        $response->assertSessionHasErrors('assignee_id');
    }
}
