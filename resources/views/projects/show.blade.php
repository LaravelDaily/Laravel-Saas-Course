<x-layouts.app :title="__('View Project')">
    <div class="p-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <flux:heading size="xl">{{ $project->name }}</flux:heading>
                <flux:text class="mt-2 text-zinc-600 dark:text-zinc-300">
                    {{ __('Project Details') }}
                </flux:text>
            </div>
            <div class="flex items-center gap-2">
                @can('update', $project)
                    <flux:button variant="primary" :href="route('projects.edit', $project)" icon="pencil">
                        {{ __('Edit') }}
                    </flux:button>
                @endcan
                <flux:button variant="ghost" :href="route('projects.index')">
                    {{ __('Back to Projects') }}
                </flux:button>
            </div>
        </div>

        <div class="mx-auto max-w-2xl">
            <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <dl class="space-y-6">
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Name') }}</dt>
                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $project->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Description') }}</dt>
                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $project->description ?: __('No description provided.') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Created') }}</dt>
                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $project->created_at->format('M d, Y \a\t H:i') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Last Updated') }}</dt>
                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $project->updated_at->format('M d, Y \a\t H:i') }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-layouts.app>



