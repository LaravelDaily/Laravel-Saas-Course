<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Accept Invitation')" :description="__('Set your password to complete your account setup')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="accept" class="flex flex-col gap-6">
        <!-- Name (Disabled) -->
        <flux:input
            :label="__('Name')"
            type="text"
            disabled
            :value="$invitation->name"
        />

        <!-- Email Address (Disabled) -->
        <flux:input
            :label="__('Email address')"
            type="email"
            disabled
            :value="$invitation->email"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autofocus
            autocomplete="new-password"
            :placeholder="__('Password')"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
            viewable
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Accept & Create Account') }}
            </flux:button>
        </div>
    </form>
</div>
