<?php

namespace Tests\Unit;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Bot;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskDomainTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_can_be_assigned_to_a_bot_and_created_by_a_user(): void
    {
        $user = User::factory()->create();
        $bot = Bot::factory()->create();

        $task = Task::factory()
            ->for($user, 'creator')
            ->assignedToBot($bot)
            ->create([
                'priority' => TaskPriority::High,
            ]);

        $this->assertTrue($task->creator->is($user));
        $this->assertTrue($task->assignee->is($bot));
        $this->assertSame(TaskPriority::High, $task->priority);
        $this->assertSame(TaskStatus::Open, $task->status);
    }

    public function test_task_comment_can_be_authored_by_a_bot_and_marked_as_delivery(): void
    {
        $bot = Bot::factory()->create();
        $task = Task::factory()->create();

        $comment = TaskComment::factory()
            ->for($task)
            ->byBot($bot)
            ->asDelivery()
            ->create();

        $this->assertTrue($comment->author->is($bot));
        $this->assertTrue($comment->is_delivery);
        $this->assertTrue($comment->task->is($task));
    }

    public function test_parent_task_cannot_be_closed_while_a_subtask_is_open(): void
    {
        $parent = Task::factory()->create();
        Task::factory()->for($parent, 'parentTask')->create([
            'status' => TaskStatus::InProgress,
        ]);

        $parent->load('subtasks');

        $this->assertFalse($parent->canBeClosed());
        $this->assertTrue($parent->hasOpenSubtasks());
    }

    public function test_parent_task_can_be_closed_when_all_subtasks_are_closed(): void
    {
        $parent = Task::factory()->create();
        Task::factory()->for($parent, 'parentTask')->create([
            'status' => TaskStatus::Done,
            'closed_at' => now(),
        ]);
        Task::factory()->for($parent, 'parentTask')->create([
            'status' => TaskStatus::Cancelled,
            'closed_at' => now(),
        ]);

        $parent->load('subtasks');

        $this->assertTrue($parent->canBeClosed());
        $this->assertFalse($parent->hasOpenSubtasks());
    }
}
