<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBotRequest;
use App\Models\Bot;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class BotController extends Controller
{
    public function index(): View
    {
        $bots = request()->user()
            ->bots()
            ->withCount(['assignedTasks', 'createdTasks'])
            ->latest()
            ->get();

        return view('bots.index', [
            'bots' => $bots,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Bot::class);

        return view('bots.create');
    }

    public function store(StoreBotRequest $request): RedirectResponse
    {
        $bot = $request->user()->bots()->create([
            'name' => $request->string('name')->value(),
            'slug' => $this->generateUniqueSlug($request->string('name')->value()),
            'description' => $request->input('description'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('bots.show', $bot)
            ->with('success', __('bots.messages.created'));
    }

    public function show(Bot $bot): View
    {
        $this->authorize('view', $bot);

        $bot->loadCount(['assignedTasks', 'createdTasks']);
        $tokens = $bot->tokens()->latest()->get();

        return view('bots.show', [
            'bot' => $bot,
            'tokens' => $tokens,
        ]);
    }

    public function destroy(Bot $bot): RedirectResponse
    {
        $this->authorize('delete', $bot);

        $bot->tokens()->delete();
        $bot->delete();

        return redirect()
            ->route('bots.index')
            ->with('success', __('bots.messages.deleted'));
    }

    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'bot';
        $slug = $baseSlug;
        $suffix = 2;

        while (Bot::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
