@php
    $selectedAssignee = old('assignee');

    if (! $selectedAssignee && isset($task)) {
        $selectedAssignee = $task->assignee instanceof \App\Models\Bot
            ? "bot:{$task->assignee->getKey()}"
            : "user:{$task->assignee->getKey()}";
    }
@endphp

<div class="space-y-6">
    <div>
        <x-input-label for="title" :value="__('tasks.form.title')" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $task->title ?? '')" required />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" :value="__('tasks.form.description')" />
        <textarea
            id="description"
            name="description"
            rows="5"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >{{ old('description', $task->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <x-input-label for="priority" :value="__('tasks.form.priority')" />
            <select
                id="priority"
                name="priority"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                @foreach (\App\Enums\TaskPriority::cases() as $priority)
                    <option value="{{ $priority->value }}" @selected(old('priority', isset($task) ? $task->priority->value : \App\Enums\TaskPriority::Normal->value) === $priority->value)>
                        {{ __('tasks.priority.'.$priority->value) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="requested_start_at" :value="__('tasks.form.requested_start_at')" />
            <x-text-input
                id="requested_start_at"
                name="requested_start_at"
                type="datetime-local"
                class="mt-1 block w-full"
                :value="old('requested_start_at', isset($task) && $task->requested_start_at ? $task->requested_start_at->format('Y-m-d\TH:i') : '')"
            />
            <x-input-error :messages="$errors->get('requested_start_at')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="assignee" :value="__('tasks.form.assignee')" />
        <select
            id="assignee"
            name="assignee"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
        >
            @foreach ($assigneeOptions as $option)
                <option value="{{ $option['value'] }}" @selected($selectedAssignee === $option['value'])>
                    {{ $option['label'] }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('assignee')" class="mt-2" />
        <x-input-error :messages="$errors->get('assignee_type')" class="mt-2" />
        <x-input-error :messages="$errors->get('assignee_id')" class="mt-2" />
    </div>
</div>
