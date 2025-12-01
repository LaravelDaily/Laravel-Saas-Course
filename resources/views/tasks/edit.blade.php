<x-layouts.app :title="__('Edit Task')">
    <div class="p-6">
        <div class="mb-6">
            <flux:heading size="xl">{{ __('Edit Task') }}</flux:heading>
            <flux:text class="mt-2 text-zinc-600 dark:text-zinc-300">
                {{ __('Update the task details.') }}
            </flux:text>
        </div>

        <div class="mx-auto max-w-2xl">
            <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <flux:input
                            name="name"
                            :label="__('Name')"
                            type="text"
                            required
                            autofocus
                            :placeholder="__('Task name')"
                            :value="old('name', $task->name)"
                        />
                        @error('name')
                            <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    <div>
                        <flux:textarea
                            name="description"
                            :label="__('Description')"
                            :placeholder="__('Task description (optional)')"
                            rows="4"
                        >{{ old('description', $task->description) }}</flux:textarea>
                        @error('description')
                            <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <flux:button variant="ghost" :href="route('tasks.index')">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button variant="primary" type="submit" icon="check">
                            {{ __('Update Task') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
