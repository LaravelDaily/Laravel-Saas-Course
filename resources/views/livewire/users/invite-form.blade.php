<div>
    <div class="mb-6">
        <flux:heading size="xl">{{ __('Invite a teammate') }}</flux:heading>
        <flux:text class="mt-2 text-zinc-600 dark:text-zinc-300">
            {{ __('Send an invitation to join your organization. The invitee will confirm their password to finish setting up access.') }}
        </flux:text>
    </div>

    <div class="mx-auto max-w-2xl">
        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <form wire:submit="submit" class="space-y-6">
                <div>
                    <flux:input
                        wire:model="name"
                        name="name"
                        :label="__('Name')"
                        type="text"
                        required
                        autofocus
                        :placeholder="__('Full name')"
                    />
                    @error('name')
                        <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                    @enderror
                </div>

                <div>
                    <flux:input
                        wire:model="email"
                        name="email"
                        :label="__('Email address')"
                        type="email"
                        required
                        placeholder="email@example.com"
                    />
                    @error('email')
                        <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                    @enderror
                </div>

                <div>
                    <flux:select
                        wire:model.live="role"
                        name="role"
                        :label="__('Role')"
                        required
                    >
                        @foreach(\App\Enums\RoleEnum::cases() as $roleCase)
                            <option value="{{ $roleCase->value }}">{{ __($roleCase->label()) }}</option>
                        @endforeach
                    </flux:select>
                    @error('role')
                        <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-4">
                    <flux:button variant="ghost" :href="route('users.index')">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button variant="primary" type="submit" icon="user-plus">
                        {{ __('Send Invitation') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
