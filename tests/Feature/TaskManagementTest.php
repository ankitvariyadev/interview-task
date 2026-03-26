<?php

declare(strict_types=1);

use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use App\Role;
use App\TaskStatus;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role as RoleModel;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    RoleModel::findOrCreate(Role::Admin->value, 'web');
    RoleModel::findOrCreate(Role::User->value, 'web');
});

function makeAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole(Role::Admin->value);

    return $user;
}

function makeStandardUser(): User
{
    $user = User::factory()->create();
    $user->assignRole(Role::User->value);

    return $user;
}

it('allows admins to create and assign tasks through the api', function () {
    $admin = makeAdmin();
    $assignee = makeStandardUser();

    $response = $this->actingAs($admin)->postJson(route('api.tasks.store'), [
        'title' => 'Prepare sprint report',
        'description' => 'Compile weekly delivery notes for the team.',
        'user_id' => $assignee->id,
        'status' => TaskStatus::InProgress->value,
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.title', 'Prepare sprint report')
        ->assertJsonPath('data.assigned_user_id', $assignee->id)
        ->assertJsonPath('data.status', TaskStatus::InProgress->value);

    $task = Task::query()->firstWhere('title', 'Prepare sprint report');

    expect($task)
        ->not->toBeNull()
        ->and($task?->user_id)->toBe($assignee->id);
});

it('lets admins list users and subtasks', function () {
    $admin = makeAdmin();
    $assignee = makeStandardUser();
    $task = Task::factory()->for($assignee, 'assignedUser')->create();
    Subtask::factory()->for($task)->create();

    $this->actingAs($admin)
        ->getJson(route('api.users.index'))
        ->assertSuccessful()
        ->assertJsonCount(1, 'data');

    $this->actingAs($admin)
        ->getJson(route('api.subtasks.index'))
        ->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

it('limits users to their assigned tasks and supports filters', function () {
    $user = makeStandardUser();
    $otherUser = makeStandardUser();

    Task::factory()->for($user, 'assignedUser')->pending()->create([
        'title' => 'Inbox cleanup',
        'description' => 'General housekeeping',
    ]);

    Task::factory()->for($otherUser, 'assignedUser')->completed()->create([
        'title' => 'Hidden task',
        'description' => 'This should not show up',
    ]);

    Task::factory()->for($user, 'assignedUser')->completed()->create([
        'title' => 'Release note review',
        'description' => 'Finalize the completed release note checklist',
    ]);

    $this->actingAs($user)
        ->getJson(route('api.tasks.index', [
            'search' => 'Release',
            'status' => TaskStatus::Completed->value,
        ]))
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Release note review');
});

it('allows users to update status and create subtasks on assigned tasks', function () {
    $user = makeStandardUser();
    $task = Task::factory()->for($user, 'assignedUser')->pending()->create();

    $this->actingAs($user)
        ->patchJson(route('api.tasks.update', $task), [
            'status' => TaskStatus::Completed->value,
        ])
        ->assertSuccessful()
        ->assertJsonPath('data.status', TaskStatus::Completed->value);

    expect($task->refresh()->status)->toBe(TaskStatus::Completed);

    $this->actingAs($user)
        ->postJson(route('api.tasks.subtasks.store', $task), [
            'title' => 'Verify completion handoff',
            'description' => 'Confirm the final checklist with the admin.',
            'status' => TaskStatus::Pending->value,
        ])
        ->assertCreated()
        ->assertJsonPath('data.title', 'Verify completion handoff');

    expect($task->subtasks()->count())->toBe(1);
});

it('supports unlimited nested subtasks within the same task hierarchy', function () {
    $user = makeStandardUser();
    $task = Task::factory()->for($user, 'assignedUser')->pending()->create();

    $rootSubtaskResponse = $this->actingAs($user)
        ->postJson(route('api.tasks.subtasks.store', $task), [
            'title' => 'Prepare rollout',
        ])
        ->assertCreated()
        ->assertJsonPath('data.parent_subtask_id', null);

    $rootSubtaskId = $rootSubtaskResponse->json('data.id');

    $childSubtaskResponse = $this->actingAs($user)
        ->postJson(route('api.tasks.subtasks.store', $task), [
            'title' => 'Confirm stakeholders',
            'parent_subtask_id' => $rootSubtaskId,
        ])
        ->assertCreated()
        ->assertJsonPath('data.parent_subtask_id', $rootSubtaskId);

    $childSubtaskId = $childSubtaskResponse->json('data.id');

    $grandchildSubtaskResponse = $this->actingAs($user)
        ->postJson(route('api.tasks.subtasks.store', $task), [
            'title' => 'Schedule launch call',
            'parent_subtask_id' => $childSubtaskId,
        ])
        ->assertCreated()
        ->assertJsonPath('data.parent_subtask_id', $childSubtaskId);

    $grandchildSubtaskId = $grandchildSubtaskResponse->json('data.id');

    $this->actingAs($user)
        ->getJson(route('api.tasks.show', $task))
        ->assertSuccessful()
        ->assertJsonPath('data.subtasks_count', 3)
        ->assertJsonCount(1, 'data.subtasks')
        ->assertJsonPath('data.subtasks.0.id', $rootSubtaskId)
        ->assertJsonPath('data.subtasks.0.parent_subtask_id', null)
        ->assertJsonCount(1, 'data.subtasks.0.subtasks')
        ->assertJsonPath('data.subtasks.0.subtasks.0.id', $childSubtaskId)
        ->assertJsonPath('data.subtasks.0.subtasks.0.parent_subtask_id', $rootSubtaskId)
        ->assertJsonCount(1, 'data.subtasks.0.subtasks.0.subtasks')
        ->assertJsonPath('data.subtasks.0.subtasks.0.subtasks.0.id', $grandchildSubtaskId)
        ->assertJsonPath('data.subtasks.0.subtasks.0.subtasks.0.parent_subtask_id', $childSubtaskId);

    expect($task->subtasks()->count())->toBe(3);
});

it('rejects nested subtasks when the chosen parent belongs to another task', function () {
    $user = makeStandardUser();
    $task = Task::factory()->for($user, 'assignedUser')->pending()->create();
    $otherTask = Task::factory()->for($user, 'assignedUser')->pending()->create();
    $otherTaskSubtask = Subtask::factory()->for($otherTask)->create();

    $this->actingAs($user)
        ->postJson(route('api.tasks.subtasks.store', $task), [
            'title' => 'Invalid nested subtask',
            'parent_subtask_id' => $otherTaskSubtask->id,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('parent_subtask_id');
});

it('prevents users from managing tasks that are not assigned to them', function () {
    $user = makeStandardUser();
    $otherUser = makeStandardUser();
    $task = Task::factory()->for($otherUser, 'assignedUser')->pending()->create();

    $this->actingAs($user)
        ->getJson(route('api.tasks.show', $task))
        ->assertForbidden();

    $this->actingAs($user)
        ->patchJson(route('api.tasks.update', $task), [
            'status' => TaskStatus::Completed->value,
        ])
        ->assertForbidden();

    $this->actingAs($user)
        ->postJson(route('api.tasks.subtasks.store', $task), [
            'title' => 'Unauthorized subtask',
        ])
        ->assertForbidden();
});
