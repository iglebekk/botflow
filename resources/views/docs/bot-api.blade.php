<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('docs.bot_api.title') }}
            </h2>
            <p class="text-sm text-gray-500">
                {{ __('docs.bot_api.subtitle') }}
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <p class="text-sm text-gray-600">
                        {{ __('docs.bot_api.source', ['path' => 'doc/bot-api.md']) }}
                    </p>
                </div>

                <div class="px-6 py-6">
                    <pre class="overflow-x-auto whitespace-pre-wrap text-sm leading-6 text-gray-800">{{ $content }}</pre>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
