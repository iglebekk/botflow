<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBotTokenRequest;
use App\Models\Bot;
use Illuminate\Http\RedirectResponse;

class BotTokenController extends Controller
{
    public function store(StoreBotTokenRequest $request, Bot $bot): RedirectResponse
    {
        $token = $bot->createToken($request->string('token_name')->value(), ['*']);

        return redirect()
            ->route('bots.show', $bot)
            ->with('success', __('bots.messages.token_created'))
            ->with('plain_text_token', $token->plainTextToken)
            ->with('plain_text_token_name', $request->string('token_name')->value());
    }
}
