<?php

namespace Tests\Unit;

use App\Models\Bot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BotTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_bot_belongs_to_its_owner(): void
    {
        $user = User::factory()->create();
        $bot = Bot::factory()->for($user, 'owner')->create();

        $this->assertTrue($bot->owner->is($user));
        $this->assertCount(1, $user->bots);
    }

    public function test_bot_can_issue_personal_access_tokens(): void
    {
        $bot = Bot::factory()->create();

        $token = $bot->createToken('worker', ['tasks:read']);

        $this->assertNotEmpty($token->plainTextToken);
        $this->assertSame('worker', $bot->tokens()->sole()->name);
    }
}
