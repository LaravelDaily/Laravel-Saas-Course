<?php

use App\Enums\RoleEnum;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Organization;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => RoleEnum::SuperAdmin->value]);
    Role::firstOrCreate(['name' => RoleEnum::Admin->value]);
    Role::firstOrCreate(['name' => RoleEnum::User->value]);
    Role::firstOrCreate(['name' => RoleEnum::Viewer->value]);

    $adminUser = User::factory()->create(['email' => 'admin@test.com']);
    $adminUser->assignRole(RoleEnum::Admin);

    $this->actingAs($adminUser);
});

test('can render user list page', function () {
    Livewire::test(ListUsers::class)
        ->assertSuccessful();
});

test('can list users', function () {
    $users = User::factory()->count(10)->create();

    Livewire::test(ListUsers::class)
        ->assertCanSeeTableRecords($users);
});

test('can search users by name', function () {
    $users = User::factory()->count(3)->create();
    $specificUser = User::factory()->create(['name' => 'John Specific Doe']);

    Livewire::test(ListUsers::class)
        ->searchTable('Specific')
        ->assertCanSeeTableRecords([$specificUser])
        ->assertCanNotSeeTableRecords($users);
});

test('can search users by email', function () {
    $users = User::factory()->count(3)->create();
    $specificUser = User::factory()->create(['email' => 'unique@example.com']);

    Livewire::test(ListUsers::class)
        ->searchTable('unique@example.com')
        ->assertCanSeeTableRecords([$specificUser])
        ->assertCanNotSeeTableRecords($users);
});

test('can filter users with email verified', function () {
    $verifiedUser = User::factory()->create(['email_verified_at' => now()]);
    $unverifiedUser = User::factory()->create(['email_verified_at' => null]);

    Livewire::test(ListUsers::class)
        ->filterTable('verified')
        ->assertCanSeeTableRecords([$verifiedUser])
        ->assertCanNotSeeTableRecords([$unverifiedUser]);
});

test('can render create user page', function () {
    Livewire::test(CreateUser::class)
        ->assertSuccessful();
});

test('can create user', function () {
    $organization = Organization::factory()->create();

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => RoleEnum::User->value,
            'organization_id' => $organization->id,
            'email_notifications' => true,
        ])
        ->call('create')
        ->assertNotified();

    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();

    $user = User::where('email', 'test@example.com')->first();
    expect($user->hasRole(RoleEnum::User))->toBeTrue();
});

test('can validate user creation', function () {
    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => '',
            'email' => 'invalid-email',
            'password' => '',
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'email' => 'email',
            'password' => 'required',
        ]);
});

test('can render edit user page', function () {
    $user = User::factory()->create();

    Livewire::test(EditUser::class, ['record' => $user->id])
        ->assertSuccessful();
});

test('can update user', function () {
    $user = User::factory()->create();
    $newOrganization = Organization::factory()->create();

    Livewire::test(EditUser::class, ['record' => $user->id])
        ->fillForm([
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => RoleEnum::Admin->value,
            'organization_id' => $newOrganization->id,
        ])
        ->call('save')
        ->assertNotified();

    $user->refresh();

    expect($user->name)->toBe('Updated Name')
        ->and($user->email)->toBe('updated@example.com')
        ->and($user->hasRole(RoleEnum::Admin))->toBeTrue()
        ->and($user->organization_id)->toBe($newOrganization->id);
});

test('can validate user update', function () {
    $user = User::factory()->create();

    Livewire::test(EditUser::class, ['record' => $user->id])
        ->fillForm([
            'name' => '',
            'email' => 'invalid-email',
        ])
        ->call('save')
        ->assertHasFormErrors([
            'name' => 'required',
            'email' => 'email',
        ]);
});

test('can delete user', function () {
    $user = User::factory()->create();

    Livewire::test(EditUser::class, ['record' => $user->id])
        ->callAction('delete');

    expect(User::where('id', $user->id)->exists())->toBeFalse();
});

test('can send notification action', function () {
    $user = User::factory()->create();

    Livewire::test(ListUsers::class)
        ->callTableAction('send_notification', $user);
});

test('super admin can access filament panel', function () {
    $superAdminUser = User::factory()->asSuperAdmin()->create(['email' => 'superadmin@test.com']);

    $panel = Filament::getPanel('admin');

    expect($superAdminUser->canAccessPanel($panel))->toBeTrue();
});

test('admin cannot access filament panel', function () {
    $adminUser = User::factory()->asAdmin()->create(['email' => 'admin2@test.com']);

    $panel = Filament::getPanel('admin');

    expect($adminUser->canAccessPanel($panel))->toBeFalse();
});

test('regular user cannot access filament panel', function () {
    $regularUser = User::factory()->asUser()->create(['email' => 'user@test.com']);

    $panel = Filament::getPanel('admin');

    expect($regularUser->canAccessPanel($panel))->toBeFalse();
});
