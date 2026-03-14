<div class="space-y-6">
    <div>
        <x-input-label for="name" :value="__('bots.form.name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $bot->name ?? '')" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" :value="__('bots.form.description')" />
        <textarea
            id="description"
            name="description"
            rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >{{ old('description', $bot->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input
            type="checkbox"
            name="is_active"
            value="1"
            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
            @checked(old('is_active', $bot->is_active ?? true))
        >
        <span>{{ __('bots.form.active') }}</span>
    </label>
</div>
