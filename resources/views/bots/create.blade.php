<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('bots.create.title') }}
            </h2>
            <p class="text-sm text-gray-500">
                {{ __('bots.create.subtitle') }}
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('bots.store') }}" class="p-6 space-y-6">
                    @csrf

                    @include('bots._form')

                    <div class="flex items-center gap-3">
                        <x-primary-button>{{ __('bots.form.save') }}</x-primary-button>

                        <a href="{{ route('bots.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('bots.form.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
