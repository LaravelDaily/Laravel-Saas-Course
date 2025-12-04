<?php

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;

test('guests cannot access projects index', function () {
    $this->get(route('projects.index'))->assertRedirect(route('login'));
});

test('admin users can view projects index', function () {
    $adminUser = User::factory()->asAdmin()->create();

    $this->actingAs($adminUser)
        ->get(route('projects.index'))
        ->assertSuccessful()
        ->assertSee('Projects');
});

test('regular users can view projects index', function () {
    $regularUser = User::factory()->asUser()->create();

    $this->actingAs($regularUser)
        ->get(route('projects.index'))
        ->assertSuccessful()
        ->assertSee('Projects');
});

test('viewers can view projects index', function () {
    $viewerUser = User::factory()->asViewer()->create();

    $this->actingAs($viewerUser)
        ->get(route('projects.index'))
        ->assertSuccessful()
        ->assertSee('Projects');
});

test('users can only see projects from their organization', function () {
    $organization1 = Organization::factory()->create(['name' => 'Org 1']);
    $organization2 = Organization::factory()->create(['name' => 'Org 2']);

    $user1 = User::factory()->asAdmin()->create(['organization_id' => $organization1->id]);
    $user2 = User::factory()->asAdmin()->create(['organization_id' => $organization2->id]);

    $project1 = Project::factory()->create([
        'name' => 'Org 1 Project',
        'organization_id' => $organization1->id,
        'user_id' => $user1->id,
    ]);

    $project2 = Project::factory()->create([
        'name' => 'Org 2 Project',
        'organization_id' => $organization2->id,
        'user_id' => $user2->id,
    ]);

    $this->actingAs($user1)
        ->get(route('projects.index'))
        ->assertSuccessful()
        ->assertSee('Org 1 Project')
        ->assertDontSee('Org 2 Project');
});

test('admin users can view create project page', function () {
    $adminUser = User::factory()->asAdmin()->create();

    $this->actingAs($adminUser)
        ->get(route('projects.create'))
        ->assertSuccessful()
        ->assertSee('Create New Project');
});

test('regular users can view create project page', function () {
    $regularUser = User::factory()->asUser()->create();

    $this->actingAs($regularUser)
        ->get(route('projects.create'))
        ->assertSuccessful()
        ->assertSee('Create New Project');
});

test('viewers cannot view create project page', function () {
    $viewerUser = User::factory()->asViewer()->create();

    $this->actingAs($viewerUser)
        ->get(route('projects.create'))
        ->assertForbidden();
});

test('admin users can create a project', function () {
    $adminUser = User::factory()->asAdmin()->create();

    $this->actingAs($adminUser)
        ->post(route('projects.store'), [
            'name' => 'New Project',
            'description' => 'Project description',
        ])
        ->assertRedirect(route('projects.index'))
        ->assertSessionHas('success', 'Project created successfully.');

    $this->assertDatabaseHas('projects', [
        'name' => 'New Project',
        'description' => 'Project description',
        'user_id' => $adminUser->id,
        'organization_id' => $adminUser->organization_id,
    ]);
});

test('viewers cannot create a project', function () {
    $viewerUser = User::factory()->asViewer()->create();

    $this->actingAs($viewerUser)
        ->post(route('projects.store'), [
            'name' => 'New Project',
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('projects', [
        'name' => 'New Project',
    ]);
});

test('project name is required when creating', function () {
    $adminUser = User::factory()->asAdmin()->create();

    $this->actingAs($adminUser)
        ->post(route('projects.store'), [
            'name' => '',
            'description' => 'Description without name',
        ])
        ->assertSessionHasErrors(['name']);

    $this->assertDatabaseMissing('projects', [
        'description' => 'Description without name',
    ]);
});

test('admin users can view project show page', function () {
    $adminUser = User::factory()->asAdmin()->create();
    $project = Project::factory()->create([
        'organization_id' => $adminUser->organization_id,
        'user_id' => $adminUser->id,
    ]);

    $this->actingAs($adminUser)
        ->get(route('projects.show', $project))
        ->assertSuccessful()
        ->assertSee($project->name);
});

test('viewers can view project show page', function () {
    $viewerUser = User::factory()->asViewer()->create();
    $project = Project::factory()->create([
        'organization_id' => $viewerUser->organization_id,
        'user_id' => $viewerUser->id,
    ]);

    $this->actingAs($viewerUser)
        ->get(route('projects.show', $project))
        ->assertSuccessful()
        ->assertSee($project->name);
});

test('users cannot view projects from other organizations', function () {
    $organization1 = Organization::factory()->create();
    $organization2 = Organization::factory()->create();

    $user1 = User::factory()->asAdmin()->create(['organization_id' => $organization1->id]);
    $user2 = User::factory()->asAdmin()->create(['organization_id' => $organization2->id]);

    $project = Project::factory()->create([
        'organization_id' => $organization2->id,
        'user_id' => $user2->id,
    ]);

    $this->actingAs($user1)
        ->get(route('projects.show', $project))
        ->assertForbidden();
});

test('admin users can view edit project page', function () {
    $adminUser = User::factory()->asAdmin()->create();
    $project = Project::factory()->create([
        'organization_id' => $adminUser->organization_id,
        'user_id' => $adminUser->id,
    ]);

    $this->actingAs($adminUser)
        ->get(route('projects.edit', $project))
        ->assertSuccessful()
        ->assertSee('Edit Project')
        ->assertSee($project->name);
});

test('viewers cannot view edit project page', function () {
    $viewerUser = User::factory()->asViewer()->create();
    $project = Project::factory()->create([
        'organization_id' => $viewerUser->organization_id,
        'user_id' => $viewerUser->id,
    ]);

    $this->actingAs($viewerUser)
        ->get(route('projects.edit', $project))
        ->assertForbidden();
});

test('admin users can update projects', function () {
    $adminUser = User::factory()->asAdmin()->create();
    $project = Project::factory()->create([
        'name' => 'Original Name',
        'description' => 'Original Description',
        'organization_id' => $adminUser->organization_id,
        'user_id' => $adminUser->id,
    ]);

    $this->actingAs($adminUser)
        ->put(route('projects.update', $project), [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ])
        ->assertRedirect(route('projects.index'))
        ->assertSessionHas('success', 'Project updated successfully.');

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ]);
});

test('viewers cannot update projects', function () {
    $viewerUser = User::factory()->asViewer()->create();
    $project = Project::factory()->create([
        'name' => 'Original Name',
        'organization_id' => $viewerUser->organization_id,
        'user_id' => $viewerUser->id,
    ]);

    $this->actingAs($viewerUser)
        ->put(route('projects.update', $project), [
            'name' => 'Updated Name',
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Original Name',
    ]);
});

test('users cannot update projects from other organizations', function () {
    $organization1 = Organization::factory()->create();
    $organization2 = Organization::factory()->create();

    $user1 = User::factory()->asAdmin()->create(['organization_id' => $organization1->id]);
    $user2 = User::factory()->asAdmin()->create(['organization_id' => $organization2->id]);

    $project = Project::factory()->create([
        'name' => 'Original Name',
        'organization_id' => $organization2->id,
        'user_id' => $user2->id,
    ]);

    $this->actingAs($user1)
        ->put(route('projects.update', $project), [
            'name' => 'Hacked Name',
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Original Name',
    ]);
});

test('admin users can delete projects', function () {
    $adminUser = User::factory()->asAdmin()->create();
    $project = Project::factory()->create([
        'organization_id' => $adminUser->organization_id,
        'user_id' => $adminUser->id,
    ]);

    $this->actingAs($adminUser)
        ->delete(route('projects.destroy', $project))
        ->assertRedirect(route('projects.index'))
        ->assertSessionHas('success', 'Project deleted successfully.');

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});

test('regular users cannot delete projects', function () {
    $regularUser = User::factory()->asUser()->create();
    $project = Project::factory()->create([
        'organization_id' => $regularUser->organization_id,
        'user_id' => $regularUser->id,
    ]);

    $this->actingAs($regularUser)
        ->delete(route('projects.destroy', $project))
        ->assertForbidden();

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
    ]);
});

test('viewers cannot delete projects', function () {
    $viewerUser = User::factory()->asViewer()->create();
    $project = Project::factory()->create([
        'organization_id' => $viewerUser->organization_id,
        'user_id' => $viewerUser->id,
    ]);

    $this->actingAs($viewerUser)
        ->delete(route('projects.destroy', $project))
        ->assertForbidden();

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
    ]);
});

test('users cannot delete projects from other organizations', function () {
    $organization1 = Organization::factory()->create();
    $organization2 = Organization::factory()->create();

    $user1 = User::factory()->asAdmin()->create(['organization_id' => $organization1->id]);
    $user2 = User::factory()->asAdmin()->create(['organization_id' => $organization2->id]);

    $project = Project::factory()->create([
        'organization_id' => $organization2->id,
        'user_id' => $user2->id,
    ]);

    $this->actingAs($user1)
        ->delete(route('projects.destroy', $project))
        ->assertForbidden();

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
    ]);
});
