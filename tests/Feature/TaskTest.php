<?php

use App\Models\Organization;
use App\Models\Task;
use App\Models\User;

test('guests cannot access tasks index', function () {
    $this->get(route('tasks.index'))->assertRedirect(route('login'));
});

test('authenticated users can view tasks index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('tasks.index'))
        ->assertSuccessful()
        ->assertSee('Tasks');
});

test('users can only see tasks from their organization', function () {
    $organization1 = Organization::factory()->create(['name' => 'Org 1']);
    $organization2 = Organization::factory()->create(['name' => 'Org 2']);

    $user1 = User::factory()->create(['organization_id' => $organization1->id]);
    $user2 = User::factory()->create(['organization_id' => $organization2->id]);

    $task1 = Task::factory()->create([
        'name' => 'Org 1 Task',
        'organization_id' => $organization1->id,
        'user_id' => $user1->id,
    ]);

    $task2 = Task::factory()->create([
        'name' => 'Org 2 Task',
        'organization_id' => $organization2->id,
        'user_id' => $user2->id,
    ]);

    $this->actingAs($user1)
        ->get(route('tasks.index'))
        ->assertSuccessful()
        ->assertSee('Org 1 Task')
        ->assertDontSee('Org 2 Task');
});

test('authenticated users can view create task page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('tasks.create'))
        ->assertSuccessful()
        ->assertSee('Create New Task');
});

test('users can create a task with name only', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('tasks.store'), [
            'name' => 'New Task',
        ])
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success', 'Task created successfully.');

    $this->assertDatabaseHas('tasks', [
        'name' => 'New Task',
        'description' => null,
        'user_id' => $user->id,
        'organization_id' => $user->organization_id,
    ]);
});

test('users can create a task with name and description', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('tasks.store'), [
            'name' => 'Complete Task',
            'description' => 'This is a detailed description',
        ])
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success', 'Task created successfully.');

    $this->assertDatabaseHas('tasks', [
        'name' => 'Complete Task',
        'description' => 'This is a detailed description',
        'user_id' => $user->id,
        'organization_id' => $user->organization_id,
    ]);
});

test('task name is required when creating', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('tasks.store'), [
            'name' => '',
            'description' => 'Description without name',
        ])
        ->assertSessionHasErrors(['name']);

    $this->assertDatabaseMissing('tasks', [
        'description' => 'Description without name',
    ]);
});

test('task name cannot exceed 255 characters', function () {
    $user = User::factory()->create();
    $longName = str_repeat('a', 256);

    $this->actingAs($user)
        ->post(route('tasks.store'), [
            'name' => $longName,
        ])
        ->assertSessionHasErrors(['name']);
});

test('users can view edit task page for their organization tasks', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'organization_id' => $user->organization_id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('tasks.edit', $task))
        ->assertSuccessful()
        ->assertSee('Edit Task')
        ->assertSee($task->name);
});

test('users cannot view edit page for tasks from other organizations', function () {
    $organization1 = Organization::factory()->create();
    $organization2 = Organization::factory()->create();

    $user1 = User::factory()->create(['organization_id' => $organization1->id]);
    $user2 = User::factory()->create(['organization_id' => $organization2->id]);

    $task = Task::factory()->create([
        'organization_id' => $organization2->id,
        'user_id' => $user2->id,
    ]);

    $this->actingAs($user1)
        ->get(route('tasks.edit', $task))
        ->assertForbidden();
});

test('users can update their organization tasks', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'name' => 'Original Name',
        'description' => 'Original Description',
        'organization_id' => $user->organization_id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->put(route('tasks.update', $task), [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ])
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success', 'Task updated successfully.');

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ]);
});

test('users cannot update tasks from other organizations', function () {
    $organization1 = Organization::factory()->create();
    $organization2 = Organization::factory()->create();

    $user1 = User::factory()->create(['organization_id' => $organization1->id]);
    $user2 = User::factory()->create(['organization_id' => $organization2->id]);

    $task = Task::factory()->create([
        'name' => 'Original Name',
        'organization_id' => $organization2->id,
        'user_id' => $user2->id,
    ]);

    $this->actingAs($user1)
        ->put(route('tasks.update', $task), [
            'name' => 'Hacked Name',
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'name' => 'Original Name',
    ]);
});

test('task name is required when updating', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'organization_id' => $user->organization_id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->put(route('tasks.update', $task), [
            'name' => '',
        ])
        ->assertSessionHasErrors(['name']);
});

test('users can delete their organization tasks', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'organization_id' => $user->organization_id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->delete(route('tasks.destroy', $task))
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success', 'Task deleted successfully.');

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

test('users cannot delete tasks from other organizations', function () {
    $organization1 = Organization::factory()->create();
    $organization2 = Organization::factory()->create();

    $user1 = User::factory()->create(['organization_id' => $organization1->id]);
    $user2 = User::factory()->create(['organization_id' => $organization2->id]);

    $task = Task::factory()->create([
        'organization_id' => $organization2->id,
        'user_id' => $user2->id,
    ]);

    $this->actingAs($user1)
        ->delete(route('tasks.destroy', $task))
        ->assertForbidden();

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
    ]);
});
