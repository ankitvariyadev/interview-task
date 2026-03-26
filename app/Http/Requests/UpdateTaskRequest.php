<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Task;
use App\Role;
use App\TaskStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = $this->route('task');

        return $task instanceof Task && ($this->user()?->can('update', $task) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        if ($this->user()?->hasRole(Role::Admin->value)) {
            return [
                'title' => ['sometimes', 'required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'user_id' => ['sometimes', 'required', 'integer', Rule::exists('users', 'id')],
                'status' => ['sometimes', 'required', Rule::enum(TaskStatus::class)],
            ];
        }

        return [
            'status' => ['required', Rule::enum(TaskStatus::class)],
        ];
    }
}
