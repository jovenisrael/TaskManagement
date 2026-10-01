<?php

namespace App\Services;

use App\Data\ProjectPayload;
use App\Data\ProjectQuery;
use App\Data\ProjectResult;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;

class ProjectService
{
    public function list(ProjectQuery $query): ProjectResult
    {
        $projects = $this->filtered($query)->get();

        return ProjectResult::ok($projects);
    }

    public function find(int $id): ProjectResult
    {
        $project = Project::query()->find($id);

        if ($project === null) {
            return ProjectResult::fail('Project not found.', 404);
        }

        return ProjectResult::ok($project);
    }

    public function create(ProjectPayload $payload): ProjectResult
    {
        $project = Project::query()->create($payload->toAttributes());

        return ProjectResult::ok($project, 'Project created.', 201);
    }

    public function update(int $id, ProjectPayload $payload): ProjectResult
    {
        $existing = $this->find($id);

        if (! $existing->success) {
            return $existing;
        }

        $existing->data->update($payload->toAttributes());

        return ProjectResult::ok($existing->data->refresh(), 'Project updated.');
    }

    public function delete(int $id): ProjectResult
    {
        $existing = $this->find($id);

        if (! $existing->success) {
            return $existing;
        }

        $existing->data->delete();

        return ProjectResult::ok(null, 'Project deleted.');
    }

    /**
     * @return Builder<Project>
     */
    private function filtered(ProjectQuery $query): Builder
    {
        $projects = Project::query();

        $this->applySearch($projects, $query->search);

        if ($query->status !== null) {
            $projects->where('status', $query->status->value);
        }

        if ($query->priority !== null) {
            $projects->where('priority', $query->priority->value);
        }

        $this->applySort($projects, $query);

        return $projects;
    }

    /**
     * @param  Builder<Project>  $query
     */
    private function applySearch(Builder $query, ?string $search): void
    {
        if ($search === null || $search === '') {
            return;
        }

        $term = '%'.addcslashes($search, '%_\\').'%';

        $query->where(function (Builder $inner) use ($term): void {
            $inner->where('client_name', 'like', $term)
                ->orWhere('project_name', 'like', $term)
                ->orWhere('description', 'like', $term);
        });
    }

    /**
     * @param  Builder<Project>  $query
     */
    private function applySort(Builder $query, ProjectQuery $filters): void
    {
        $direction = $filters->direction === 'desc' ? 'desc' : 'asc';

        if ($filters->sort === 'priority') {
            $query->orderByRaw("case priority when 'Low' then 1 when 'Medium' then 2 when 'High' then 3 else 4 end {$direction}");
        } elseif ($filters->sort === 'status') {
            $query->orderByRaw("case status when 'Planning' then 1 when 'In Progress' then 2 when 'On Hold' then 3 when 'Completed' then 4 else 5 end {$direction}");
        } else {
            $column = match ($filters->sort) {
                'clientName' => 'client_name',
                'projectName' => 'project_name',
                'startDate' => 'start_date',
                default => 'due_date',
            };

            $query->orderBy($column, $direction);
        }

        $query->orderBy('id');
    }
}
