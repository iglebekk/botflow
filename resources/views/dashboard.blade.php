<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <div class="space-y-1">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('tasks.dashboard.title') }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ __('tasks.dashboard.subtitle', ['count' => $taskCount]) }}
                </p>
            </div>

            <a
                href="{{ route('tasks.create') }}"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
            >
                {{ __('tasks.index.create') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-5">
                @foreach ($columns as $column)
                    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ $column['label'] }}</h3>
                                <p class="mt-1 text-sm text-gray-500">{{ $column['description'] }}</p>
                            </div>
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-semibold text-indigo-700">
                                {{ $column['count'] }}
                            </span>
                        </div>

                        <div class="mt-5 space-y-3">
                            @forelse ($column['tasks'] as $task)
                                <a href="{{ route('tasks.show', $task) }}" class="block rounded-lg border border-gray-200 p-4 transition hover:border-indigo-300 hover:shadow-sm">
                                    <div class="space-y-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="font-medium text-gray-900">{{ $task->title }}</p>
                                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                                {{ __('tasks.priority.'.$task->priority->value) }}
                                            </span>
                                        </div>

                                        @if ($task->description)
                                            <p class="text-sm text-gray-600 line-clamp-3">{{ $task->description }}</p>
                                        @endif

                                        <div class="space-y-1 text-xs text-gray-500">
                                            <p>{{ __('tasks.show.assigned_to') }}: {{ $task->assignee instanceof \App\Models\Bot ? $task->assignee->displayName() : $task->assignee->name }}</p>
                                            <p>{{ __('tasks.show.created_by') }}: {{ $task->creator instanceof \App\Models\Bot ? $task->creator->displayName() : $task->creator->name }}</p>
                                            <p>{{ __('tasks.index.subtasks', ['count' => $task->subtasks->count()]) }}</p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="rounded-lg border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500">
                                    {{ __('tasks.dashboard.empty_column') }}
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
