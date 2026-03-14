<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <div class="space-y-1">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $task->title }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ __('tasks.show.title') }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('tasks.edit', $task) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                    {{ __('tasks.show.edit') }}
                </a>

                <a href="{{ route('tasks.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    {{ __('tasks.show.back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('tasks.show.summary') }}</h3>

                        <dl class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('tasks.show.status') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ __('tasks.status.'.$task->status->value) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('tasks.show.priority') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ __('tasks.priority.'.$task->priority->value) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('tasks.show.created_by') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $task->creator instanceof \App\Models\Bot ? $task->creator->displayName() : $task->creator->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('tasks.show.assigned_to') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $task->assignee instanceof \App\Models\Bot ? $task->assignee->displayName() : $task->assignee->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('tasks.show.requested_start_at') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $task->requested_start_at?->format('Y-m-d H:i') ?? '—' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('tasks.show.parent_task') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if ($task->parentTask)
                                        <a href="{{ route('tasks.show', $task->parentTask) }}" class="text-indigo-600 hover:text-indigo-800">
                                            #{{ $task->parentTask->id }} — {{ $task->parentTask->title }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        @if ($task->description)
                            <div class="mt-6 rounded-lg bg-gray-50 p-4 text-sm text-gray-700">
                                {{ $task->description }}
                            </div>
                        @endif
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('tasks.show.comments') }}</h3>
                        </div>

                        <form method="POST" action="{{ route('tasks.comments.store', $task) }}" class="mt-4 space-y-4">
                            @csrf

                            <div>
                                <textarea
                                    name="body"
                                    rows="4"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="{{ __('tasks.show.comment_placeholder') }}"
                                >{{ old('body') }}</textarea>
                                <x-input-error :messages="$errors->get('body')" class="mt-2" />
                            </div>

                            <label class="flex items-center gap-2 text-sm text-gray-600">
                                <input type="checkbox" name="is_delivery" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span>{{ __('tasks.show.delivery') }}</span>
                            </label>

                            <x-primary-button>{{ __('tasks.comment_form.submit') }}</x-primary-button>
                        </form>

                        <div class="mt-6 space-y-4">
                            @forelse ($task->comments as $comment)
                                <div class="rounded-lg border border-gray-200 p-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $comment->author instanceof \App\Models\Bot ? $comment->author->displayName() : $comment->author->name }}</div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            @if ($comment->is_delivery)
                                                <span class="rounded-full bg-green-50 px-2 py-1 font-medium text-green-700">Delivery</span>
                                            @endif
                                            <span>{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                                        </div>
                                    </div>
                                    <p class="mt-3 text-sm text-gray-700">{{ $comment->body }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">{{ __('tasks.show.no_comments') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('tasks.show.status') }}</h3>

                        <form method="POST" action="{{ route('tasks.status.update', $task) }}" class="mt-4 space-y-4">
                            @csrf
                            @method('PATCH')

                            <select
                                name="status"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                @foreach (\App\Enums\TaskStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($task->status === $status)>
                                        {{ __('tasks.status.'.$status->value) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />

                            @if (! $task->canBeClosed())
                                <p class="text-sm text-amber-700">
                                    {{ __('tasks.validation.parent_requires_closed_subtasks') }}
                                </p>
                            @endif

                            <x-primary-button>{{ __('tasks.status_form.submit') }}</x-primary-button>
                        </form>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('tasks.show.subtasks') }}</h3>

                        <div class="mt-4 space-y-3">
                            @forelse ($task->subtasks as $subtask)
                                <a href="{{ route('tasks.show', $subtask) }}" class="block rounded-lg border border-gray-200 p-4 hover:border-indigo-300">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $subtask->title }}</p>
                                            <p class="text-sm text-gray-500">{{ $subtask->assignee instanceof \App\Models\Bot ? $subtask->assignee->displayName() : $subtask->assignee->name }}</p>
                                        </div>
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">
                                            {{ __('tasks.status.'.$subtask->status->value) }}
                                        </span>
                                    </div>
                                </a>
                            @empty
                                <p class="text-sm text-gray-500">{{ __('tasks.show.no_subtasks') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('tasks.show.new_subtask') }}</h3>

                        <form method="POST" action="{{ route('tasks.subtasks.store', $task) }}" class="mt-4 space-y-4">
                            @csrf

                            <div>
                                <x-input-label for="subtask_title" :value="__('tasks.form.title')" />
                                <x-text-input id="subtask_title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="subtask_description" :value="__('tasks.form.description')" />
                                <textarea
                                    id="subtask_description"
                                    name="description"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="subtask_priority" :value="__('tasks.form.priority')" />
                                <select
                                    id="subtask_priority"
                                    name="priority"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    @foreach (\App\Enums\TaskPriority::cases() as $priority)
                                        <option value="{{ $priority->value }}">{{ __('tasks.priority.'.$priority->value) }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="subtask_assignee" :value="__('tasks.form.assignee')" />
                                <select
                                    id="subtask_assignee"
                                    name="assignee"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    @foreach ($assigneeOptions as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('assignee')" class="mt-2" />
                            </div>

                            <x-primary-button>{{ __('tasks.subtask_form.submit') }}</x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
