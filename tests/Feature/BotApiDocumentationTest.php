<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BotApiDocumentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_bot_api_documentation(): void
    {
        $this->get(route('docs.bot-api'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_open_bot_api_documentation_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('docs.bot-api'));

        $response->assertOk();
        $response->assertSeeText(__('docs.bot_api.title'));
        $response->assertSeeText('GET /api/tasks');
        $response->assertSeeText('POST /api/tasks/{task}/comments');
    }
}
