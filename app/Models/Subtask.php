<?php

declare(strict_types=1);

namespace App\Models;

use App\Role;
use App\TaskStatus;
use Database\Factories\SubtaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subtask extends Model
{
    /** @use HasFactory<SubtaskFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'title',
        'description',
        'status',
    ];

    /**
     * @var array<string, string>
     */
    protected $attributes = [
        'status' => TaskStatus::Pending->value,
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole(Role::Admin->value)) {
            return $query;
        }

        return $query->whereHas('task', fn (Builder $taskQuery): Builder => $taskQuery->whereBelongsTo($user, 'assignedUser'));
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (blank($search)) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($search): void {
            $builder
                ->where('title', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        });
    }

    public function scopeFilterStatus(Builder $query, ?TaskStatus $status): Builder
    {
        if (! $status instanceof TaskStatus) {
            return $query;
        }

        return $query->where('status', $status->value);
    }
}
