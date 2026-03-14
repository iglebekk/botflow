<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('tasks.edit.title') }}
            </h2>
            <p class="text-sm text-gray-500">
                {{ __('tasks.edit.subtitle') }}
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('tasks.update', $task) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PATCH')

                    @include('tasks._form', ['task' => $task])

                    <div class="flex items-center gap-3">
                        <x-primary-button>{{ __('tasks.form.update') }}</x-primary-button>

                        <a href="{{ route('tasks.show', $task) }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('tasks.form.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
