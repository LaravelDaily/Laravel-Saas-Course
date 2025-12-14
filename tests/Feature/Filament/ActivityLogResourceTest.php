<?php

use App\Enums\RoleEnum;
use App\Filament\Resources\ActivityLogs\ActivityLogResource;
use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => RoleEnum::Admin->value]);

    $adminUser = User::factory()->create(['email' => 'admin@test.com']);
    $adminUser->assignRole(RoleEnum::Admin);

    $this->actingAs($adminUser);
});

test('can render activity log list page', function () {
    Livewire::test(ListActivityLogs::class)
        ->assertSuccessful();
});

test('can list activity logs', function () {
    $activities = collect();
    for ($i = 0; $i < 10; $i++) {
        $activities->push(Activity::create([
            'log_name' => 'default',
            'description' => 'created',
            'subject_type' => User::class,
            'subject_id' => 1,
        ]));
    }

    Livewire::test(ListActivityLogs::class)
        ->assertCanSeeTableRecords($activities);
});

test('can search activity logs by description', function () {
    for ($i = 0; $i < 3; $i++) {
        Activity::create([
            'log_name' => 'default',
            'description' => 'created',
            'subject_type' => User::class,
            'subject_id' => 1,
        ]);
    }

    $specificActivity = Activity::create([
        'log_name' => 'default',
        'description' => 'special_action',
        'subject_type' => User::class,
        'subject_id' => 1,
    ]);

    Livewire::test(ListActivityLogs::class)
        ->searchTable('special_action')
        ->assertCanSeeTableRecords([$specificActivity]);
});

test('can filter activity logs by log name', function () {
    $defaultLogs = collect();
    for ($i = 0; $i < 3; $i++) {
        $defaultLogs->push(Activity::create([
            'log_name' => 'default',
            'description' => 'created',
            'subject_type' => User::class,
            'subject_id' => 1,
        ]));
    }

    $customLogs = collect();
    for ($i = 0; $i < 2; $i++) {
        $customLogs->push(Activity::create([
            'log_name' => 'custom',
            'description' => 'created',
            'subject_type' => User::class,
            'subject_id' => 1,
        ]));
    }

    Livewire::test(ListActivityLogs::class)
        ->filterTable('log_name', 'custom')
        ->assertCanSeeTableRecords($customLogs)
        ->assertCanNotSeeTableRecords($defaultLogs);
});

test('can filter activity logs by event type', function () {
    $createdLogs = collect();
    for ($i = 0; $i < 3; $i++) {
        $createdLogs->push(Activity::create([
            'log_name' => 'default',
            'description' => 'created',
            'subject_type' => User::class,
            'subject_id' => 1,
        ]));
    }

    $updatedLogs = collect();
    for ($i = 0; $i < 2; $i++) {
        $updatedLogs->push(Activity::create([
            'log_name' => 'default',
            'description' => 'updated',
            'subject_type' => User::class,
            'subject_id' => 1,
        ]));
    }

    Livewire::test(ListActivityLogs::class)
        ->filterTable('description', 'created')
        ->assertCanSeeTableRecords($createdLogs)
        ->assertCanNotSeeTableRecords($updatedLogs);
});

test('can filter activity logs by date range', function () {
    $oldActivity = Activity::create([
        'log_name' => 'default',
        'description' => 'created',
        'subject_type' => User::class,
        'subject_id' => 1,
        'created_at' => now()->subDays(10),
    ]);

    $recentActivity = Activity::create([
        'log_name' => 'default',
        'description' => 'created',
        'subject_type' => User::class,
        'subject_id' => 1,
        'created_at' => now()->subDays(2),
    ]);

    Livewire::test(ListActivityLogs::class)
        ->filterTable('created_at', [
            'created_from' => now()->subDays(5)->format('Y-m-d'),
            'created_until' => now()->format('Y-m-d'),
        ])
        ->assertCanSeeTableRecords([$recentActivity])
        ->assertCanNotSeeTableRecords([$oldActivity]);
});

test('can view activity details', function () {
    $activity = Activity::create([
        'log_name' => 'default',
        'description' => 'created',
        'subject_type' => User::class,
        'subject_id' => 1,
        'properties' => ['key' => 'value'],
    ]);

    Livewire::test(ListActivityLogs::class)
        ->callTableAction('view_properties', $activity);
});

test('cannot create activity logs from ui', function () {
    expect(ActivityLogResource::canCreate())->toBeFalse();
});
