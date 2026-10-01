<?php

namespace App\Http\Requests\Project;

use App\Data\ProjectQuery;
use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ListProjectsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'search' => $this->emptyToNull('search'),
            'status' => $this->emptyToNull('status'),
            'priority' => $this->emptyToNull('priority'),
            'sort' => $this->emptyToNull('sort') ?? ProjectQuery::DEFAULT_SORT,
            'direction' => $this->emptyToNull('direction') ?? 'asc',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(ProjectStatus::values())],
            'priority' => ['nullable', Rule::in(ProjectPriority::values())],
            'sort' => ['required', Rule::in(ProjectQuery::SORTS)],
            'direction' => ['required', Rule::in(['asc', 'desc'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $statuses = implode(', ', ProjectStatus::values());
        $priorities = implode(', ', ProjectPriority::values());
        $sorts = implode(', ', ProjectQuery::SORTS);

        return [
            'status.in' => "Status must be one of: {$statuses}.",
            'priority.in' => "Priority must be one of: {$priorities}.",
            'sort.in' => "Sort must be one of: {$sorts}.",
            'direction.in' => 'Direction must be asc or desc.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            parent::failedValidation($validator);
        }

        throw (new ValidationException($validator))->redirectTo(route('projects.index'));
    }

    private function emptyToNull(string $key): mixed
    {
        $value = $this->input($key);

        if (! is_string($value)) {
            return $value;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
