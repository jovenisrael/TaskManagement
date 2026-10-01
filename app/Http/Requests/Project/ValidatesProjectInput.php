<?php

namespace App\Http\Requests\Project;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use Illuminate\Validation\Rule;

trait ValidatesProjectInput
{
    protected function prepareForValidation(): void
    {
        $description = $this->input('description');

        $this->merge([
            'clientName' => $this->trimmed('clientName'),
            'projectName' => $this->trimmed('projectName'),
            'description' => is_string($description) && trim($description) === '' ? null : (is_string($description) ? trim($description) : $description),
            'status' => $this->trimmed('status'),
            'priority' => $this->trimmed('priority'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'clientName' => ['required', 'string', 'max:255'],
            'projectName' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(ProjectStatus::values())],
            'priority' => ['required', Rule::in(ProjectPriority::values())],
            'startDate' => ['required', 'date'],
            'dueDate' => ['required', 'date', 'after_or_equal:startDate'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $statuses = implode(', ', ProjectStatus::values());
        $priorities = implode(', ', ProjectPriority::values());

        return [
            'clientName.required' => 'Client name is required.',
            'projectName.required' => 'Project name is required.',
            'status.required' => 'Status is required.',
            'status.in' => "Status must be one of: {$statuses}.",
            'priority.required' => 'Priority is required.',
            'priority.in' => "Priority must be one of: {$priorities}.",
            'startDate.required' => 'Start date is required.',
            'startDate.date' => 'Start date must be a valid date.',
            'dueDate.required' => 'Due date is required.',
            'dueDate.date' => 'Due date must be a valid date.',
            'dueDate.after_or_equal' => 'Due date cannot be earlier than the start date.',
            'description.string' => 'Description must be text.',
            'description.max' => 'Description may not be greater than 5000 characters.',
            'clientName.max' => 'Client name may not be greater than 255 characters.',
            'projectName.max' => 'Project name may not be greater than 255 characters.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'clientName' => 'client name',
            'projectName' => 'project name',
            'startDate' => 'start date',
            'dueDate' => 'due date',
        ];
    }

    private function trimmed(string $key): mixed
    {
        $value = $this->input($key);

        return is_string($value) ? trim($value) : $value;
    }
}
