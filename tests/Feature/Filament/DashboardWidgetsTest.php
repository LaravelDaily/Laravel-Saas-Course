<?php

use App\Enums\RoleEnum;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\UserGrowthChart;
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

test('can render stats overview widget', function () {
    Livewire::test(StatsOverview::class)
        ->assertSuccessful();
});

test('stats overview displays correct user count', function () {
    User::factory()->count(15)->create();

    Livewire::test(StatsOverview::class)
        ->assertSeeHtml('Total Users');
});

test('can render user growth chart widget', function () {
    Livewire::test(UserGrowthChart::class)
        ->assertSuccessful();
});

test('can render recent activity widget', function () {
    Livewire::test(RecentActivityWidget::class)
        ->assertSuccessful();
});

test('recent activity widget displays activities', function () {
    Activity::create([
        'log_name' => 'default',
        'description' => 'created',
        'subject_type' => User::class,
        'subject_id' => 1,
        'causer_type' => User::class,
        'causer_id' => auth()->id(),
    ]);

    Activity::create([
        'log_name' => 'default',
        'description' => 'updated',
        'subject_type' => User::class,
        'subject_id' => 2,
        'causer_type' => User::class,
        'causer_id' => auth()->id(),
    ]);

    Livewire::test(RecentActivityWidget::class)
        ->assertCanSeeTableRecords(Activity::all());
});
