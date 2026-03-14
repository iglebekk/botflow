<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="space-y-1">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('tasks.index.title') }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ __('tasks.index.subtitle') }}
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @forelse ($tasks as $task)
                <a href="{{ route('tasks.show', $task) }}" class="block">
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition hover:border-indigo-300 hover:shadow">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $task->title }}</h3>
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                        {{ __('tasks.status.'.$task->status->value) }}
                                    </span>
                                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">
                                        {{ __('tasks.priority.'.$task->priority->value) }}
                                    </span>
                                </div>

                                @if ($task->description)
                                    <p class="text-sm text-gray-600">{{ $task->description }}</p>
                                @endif

                                <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                    <span>{{ __('tasks.show.assigned_to') }}: {{ $task->assignee instanceof \App\Models\Bot ? $task->assignee->displayName() : $task->assignee->name }}</span>
                                    <span>{{ __('tasks.show.created_by') }}: {{ $task->creator instanceof \App\Models\Bot ? $task->creator->displayName() : $task->creator->name }}</span>
                                    <span>{{ __('tasks.index.subtasks', ['count' => $task->subtasks->count()]) }}</span>

                                    @if ($task->parentTask)
                                        <span>{{ __('tasks.index.parent', ['id' => $task->parentTask->id]) }}</span>
                                    @endif
                                </div>
                            </div>

                            @if ($task->requested_start_at)
                                <div class="text-sm text-gray-500">
                                    {{ __('tasks.show.requested_start_at') }}:
                                    {{ $task->requested_start_at->format('Y-m-d H:i') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="rounded-lg border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-500">
                    {{ __('tasks.index.empty') }}
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
