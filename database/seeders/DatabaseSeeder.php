<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use App\Role;
use App\TaskStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role as RoleModel;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        RoleModel::findOrCreate(Role::Admin->value, 'web');
        RoleModel::findOrCreate(Role::User->value, 'web');

        $admin = User::query()->updateOrCreate(
            ['email' => 'super@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => 'Pass@123',
            ],
        );

        $admin->syncRoles([Role::Admin->value]);

        $users = collect([
            [
                'name' => 'Primary User',
                'email' => 'user@test.com',
            ],
            [
                'name' => 'Operations User',
                'email' => 'ops@test.com',
            ],
        ])->map(function (array $attributes): User {
            $user = User::query()->updateOrCreate(
                ['email' => $attributes['email']],
                [
                    'name' => $attributes['name'],
                    'password' => 'Pass@123',
                ],
            );

            $user->syncRoles([Role::User->value]);

            return $user;
        });

        $taskBlueprints = [
            [
                'title' => 'Plan onboarding checklist',
                'status' => TaskStatus::InProgress,
                'user' => $users[0],
                'subtasks' => [
                    ['title' => 'Create workspace access', 'status' => TaskStatus::Completed],
                    ['title' => 'Review delivery milestones', 'status' => TaskStatus::Pending],
                ],
            ],
            [
                'title' => 'Prepare weekly delivery report',
                'status' => TaskStatus::Pending,
                'user' => $users[0],
                'subtasks' => [
                    ['title' => 'Collect blocker updates', 'status' => TaskStatus::Pending],
                ],
            ],
            [
                'title' => 'Audit backlog priorities',
                'status' => TaskStatus::Completed,
                'user' => $users[1],
                'subtasks' => [
                    ['title' => 'Tag high-risk stories', 'status' => TaskStatus::Completed],
                    ['title' => 'Confirm owner assignments', 'status' => TaskStatus::Completed],
                ],
            ],
        ];

        foreach ($taskBlueprints as $taskBlueprint) {
            $task = Task::query()->updateOrCreate(
                [
                    'title' => $taskBlueprint['title'],
                    'user_id' => $taskBlueprint['user']->id,
                ],
                [
                    'status' => $taskBlueprint['status']->value,
                ],
            );

            foreach ($taskBlueprint['subtasks'] as $subtaskBlueprint) {
                Subtask::query()->updateOrCreate(
                    [
                        'task_id' => $task->id,
                        'title' => $subtaskBlueprint['title'],
                    ],
                    [
                        'status' => $subtaskBlueprint['status']->value,
                    ],
                );
            }
        }
    }
}
