<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Subtask;
use App\Models\Task;
use App\TaskStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subtask>
 */
class SubtaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'title' => fake()->sentence(4),
            'status' => fake()->randomElement(TaskStatus::values()),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (): array => [
            'status' => TaskStatus::Pending->value,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (): array => [
            'status' => TaskStatus::Completed->value,
        ]);
    }
}
