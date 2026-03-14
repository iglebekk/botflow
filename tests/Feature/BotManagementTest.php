<?php

namespace Tests\Feature;

use App\Models\Bot;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BotManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_bot_index(): void
    {
        $this->get(route('bots.index'))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_create_a_bot(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('bots.store'), [
            'name' => 'Research worker',
            'description' => 'Handles research subtasks.',
            'is_active' => '1',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('bots', [
            'user_id' => $user->id,
            'name' => 'Research worker',
            'is_active' => true,
        ]);
    }

    public function test_bot_index_only_lists_bots_owned_by_the_current_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownedBot = Bot::factory()->for($user, 'owner')->create(['name' => 'Owned bot']);
        Bot::factory()->for($otherUser, 'owner')->create(['name' => 'Other bot']);

        $response = $this->actingAs($user)->get(route('bots.index'));

        $response->assertOk();
        $response->assertSeeText($ownedBot->name);
        $response->assertDontSeeText('Other bot');
    }

    public function test_user_cannot_view_another_users_bot(): void
    {
        $user = User::factory()->create();
        $otherUsersBot = Bot::factory()->create();

        $this->actingAs($user)
            ->get(route('bots.show', $otherUsersBot))
            ->assertForbidden();
    }

    public function test_user_can_issue_a_token_for_their_bot_and_it_is_only_shown_once(): void
    {
        $user = User::factory()->create();
        $bot = Bot::factory()->for($user, 'owner')->create();

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->post(route('bots.tokens.store', $bot), [
                'token_name' => 'Production worker',
            ]);

        $response->assertOk();
        $response->assertSeeText(__('bots.show.plain_text_token_title'));
        $response->assertSeeText(__('bots.messages.token_created'));

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => Bot::class,
            'tokenable_id' => $bot->id,
            'name' => 'Production worker',
        ]);

        $this->actingAs($user)
            ->get(route('bots.show', $bot))
            ->assertDontSeeText(__('bots.show.plain_text_token_title'));
    }

    public function test_user_can_delete_their_bot_and_revoke_its_tokens(): void
    {
        $user = User::factory()->create();
        $bot = Bot::factory()->for($user, 'owner')->create();
        $bot->createToken('worker', ['*']);

        $response = $this->actingAs($user)
            ->delete(route('bots.destroy', $bot));

        $response->assertRedirect(route('bots.index'));
        $this->assertSoftDeleted('bots', ['id' => $bot->id]);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_type' => Bot::class,
            'tokenable_id' => $bot->id,
        ]);
    }

    public function test_deleted_bot_is_hidden_from_bot_index_but_still_visible_in_task_history(): void
    {
        $user = User::factory()->create();
        $bot = Bot::factory()->for($user, 'owner')->create(['name' => 'History bot']);
        $task = Task::factory()->assignedToBot($bot)->createdByBot($bot)->create();
        TaskComment::factory()->for($task)->byBot($bot)->create([
            'body' => 'Historical delivery',
        ]);

        $this->actingAs($user)->delete(route('bots.destroy', $bot));

        $this->actingAs($user)
            ->get(route('bots.index'))
            ->assertDontSeeText('History bot');

        $this->actingAs($user)
            ->get(route('tasks.show', $task))
            ->assertSeeText(__('bots.history.deleted_name', ['name' => 'History bot']))
            ->assertSeeText('Historical delivery');
    }

    public function test_user_cannot_delete_another_users_bot(): void
    {
        $user = User::factory()->create();
        $otherUsersBot = Bot::factory()->create();

        $this->actingAs($user)
            ->delete(route('bots.destroy', $otherUsersBot))
            ->assertForbidden();
    }
}
