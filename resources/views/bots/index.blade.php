<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="space-y-1">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('bots.index.title') }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ __('bots.index.subtitle') }}
                </p>
            </div>

            <a
                href="{{ route('bots.create') }}"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
            >
                {{ __('bots.index.create') }}
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

            @forelse ($bots as $bot)
                <a href="{{ route('bots.show', $bot) }}" class="block">
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition hover:border-indigo-300 hover:shadow">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $bot->name }}</h3>
                                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $bot->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $bot->is_active ? __('bots.show.active') : __('bots.show.inactive') }}
                                    </span>
                                </div>

                                @if ($bot->description)
                                    <p class="text-sm text-gray-600">{{ $bot->description }}</p>
                                @endif

                                <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                    <span>{{ __('bots.show.slug') }}: {{ $bot->slug }}</span>
                                    <span>{{ __('bots.index.assigned_tasks', ['count' => $bot->assigned_tasks_count]) }}</span>
                                    <span>{{ __('bots.index.created_tasks', ['count' => $bot->created_tasks_count]) }}</span>
                                </div>
                            </div>

                            <span class="text-sm font-medium text-indigo-600">
                                {{ __('bots.index.show') }}
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="rounded-lg border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-500">
                    {{ __('bots.index.empty') }}
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
