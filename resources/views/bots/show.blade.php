<x-app-layout>
    <x-slot name="header">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                <div class="space-y-1">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $bot->name }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ __('bots.show.title') }}
                </p>
            </div>

            <a href="{{ route('bots.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('bots.show.back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('plain_text_token'))
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    <p class="font-semibold">{{ __('bots.show.plain_text_token_title') }}</p>
                    <p class="mt-1">{{ __('bots.show.plain_text_token_help') }}</p>
                    <div class="mt-3 rounded-md bg-white px-4 py-3 font-mono text-xs text-gray-900 break-all">
                        {{ session('plain_text_token') }}
                    </div>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('bots.show.summary') }}</h3>

                        <dl class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('bots.show.slug') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bot->slug }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('bots.show.status') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bot->is_active ? __('bots.show.active') : __('bots.show.inactive') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('bots.index.assigned_tasks', ['count' => 0]) }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bot->assigned_tasks_count }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('bots.index.created_tasks', ['count' => 0]) }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bot->created_tasks_count }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('bots.show.last_seen') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bot->last_seen_at?->format('Y-m-d H:i') ?? __('bots.show.never_seen') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('bots.show.created_at') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bot->created_at->format('Y-m-d H:i') }}</dd>
                            </div>
                        </dl>

                        @if ($bot->description)
                            <div class="mt-6 rounded-lg bg-gray-50 p-4 text-sm text-gray-700">
                                {{ $bot->description }}
                            </div>
                        @endif
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('bots.show.tokens') }}</h3>

                        <div class="mt-4 space-y-4">
                            @forelse ($tokens as $token)
                                <div class="rounded-lg border border-gray-200 p-4">
                                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $token->name }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ __('bots.show.created_at') }}: {{ $token->created_at->format('Y-m-d H:i') }}
                                            </p>
                                        </div>
                                        <p class="text-sm text-gray-500">
                                            {{ __('bots.show.token_last_used') }}:
                                            {{ $token->last_used_at?->format('Y-m-d H:i') ?? __('bots.show.token_never_used') }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">{{ __('bots.show.no_tokens') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('bots.show.danger_zone') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('bots.show.delete_help') }}</p>

                        <form method="POST" action="{{ route('bots.destroy', $bot) }}" class="mt-4">
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500"
                                onclick="return confirm('{{ __('bots.show.delete_confirm') }}');"
                            >
                                {{ __('bots.show.delete') }}
                            </button>
                        </form>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('bots.show.new_token') }}</h3>

                        <form method="POST" action="{{ route('bots.tokens.store', $bot) }}" class="mt-4 space-y-4">
                            @csrf

                            <div>
                                <x-input-label for="token_name" :value="__('bots.show.token_name')" />
                                <x-text-input
                                    id="token_name"
                                    name="token_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('token_name', session('plain_text_token_name'))"
                                    placeholder="{{ __('bots.show.token_name_placeholder') }}"
                                    required
                                />
                                <x-input-error :messages="$errors->get('token_name')" class="mt-2" />
                            </div>

                            <x-primary-button>{{ __('bots.show.new_token') }}</x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
