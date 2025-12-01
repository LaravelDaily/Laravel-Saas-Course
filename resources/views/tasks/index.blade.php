<x-layouts.app :title="__('Tasks')">
    <div class="p-6 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <flux:heading size="xl">{{ __('Tasks') }}</flux:heading>

            <flux:button variant="primary" :href="route('tasks.create')" icon="plus">
                {{ __('New Task') }}
            </flux:button>
        </div>

        @if (session('success'))
            <flux:callout variant="success">
                {{ session('success') }}
            </flux:callout>
        @endif

        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-start text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Name') }}
                        </th>
                        <th class="px-6 py-3 text-end text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900">
                    @forelse ($tasks as $task)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $task->name }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-end text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button
                                        :href="route('tasks.edit', $task)"
                                        variant="ghost"
                                        size="sm"
                                        icon="pencil"
                                    >
                                        {{ __('Edit') }}
                                    </flux:button>
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button
                                            type="submit"
                                            variant="danger"
                                            size="sm"
                                            icon="trash"
                                            onclick="return confirm('Are you sure you want to delete this task?')"
                                        >
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-12 text-center">
                                <flux:text class="text-zinc-500 dark:text-zinc-400">
                                    {{ __('No tasks yet. Create your first task to get started.') }}
                                </flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
