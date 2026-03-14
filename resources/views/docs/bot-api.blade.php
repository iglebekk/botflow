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
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <p class="text-sm text-gray-600">
                            {{ __('docs.bot_api.source', ['path' => 'doc/bot-api.md']) }}
                        </p>

                        <button
                            type="button"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                            x-data="{ copied: false }"
                            x-on:click="
                                navigator.clipboard.writeText($refs.botApiContent.value);
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            "
                        >
                            <span x-show="! copied">{{ __('docs.bot_api.copy_action') }}</span>
                            <span x-show="copied" x-cloak>{{ __('docs.bot_api.copy_success') }}</span>
                        </button>
                    </div>
                </div>

                <div class="space-y-6 px-6 py-6">
                    <div class="space-y-2">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-700">
                            {{ __('docs.bot_api.handoff_title') }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ __('docs.bot_api.handoff_help') }}
                        </p>
                        <textarea
                            x-ref="botApiContent"
                            readonly
                            rows="18"
                            class="w-full rounded-lg border border-gray-300 bg-gray-50 p-4 font-mono text-sm leading-6 text-gray-800"
                        >{{ $content }}</textarea>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-700">
                            {{ __('docs.bot_api.preview_title') }}
                        </h3>
                        <div class="max-w-none space-y-4 text-sm leading-6 text-gray-800 [&_a]:text-indigo-600 [&_a:hover]:text-indigo-500 [&_blockquote]:border-l-4 [&_blockquote]:border-gray-300 [&_blockquote]:pl-4 [&_code]:rounded [&_code]:bg-gray-100 [&_code]:px-1.5 [&_code]:py-0.5 [&_h1]:text-2xl [&_h1]:font-semibold [&_h2]:mt-8 [&_h2]:text-xl [&_h2]:font-semibold [&_h3]:mt-6 [&_h3]:text-lg [&_h3]:font-semibold [&_li]:ml-5 [&_li]:list-disc [&_p]:text-gray-700 [&_pre]:overflow-x-auto [&_pre]:rounded-lg [&_pre]:bg-gray-950 [&_pre]:p-4 [&_pre]:text-sm [&_pre]:text-gray-100 [&_pre_code]:bg-transparent [&_pre_code]:px-0 [&_pre_code]:py-0 [&_pre_code]:text-inherit">
                            {!! $renderedContent !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
