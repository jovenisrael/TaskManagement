<?php

namespace App\Http\Controllers;

use App\Data\ProjectQuery;
use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Http\Requests\Project\ListProjectsRequest;
use App\Http\Resources\ProjectResource;
use App\Services\ProjectService;
use Inertia\Inertia;
use Inertia\Response;

class ProjectPageController extends Controller
{
    public function index(ListProjectsRequest $request, ProjectService $projects): Response
    {
        $validated = $request->validated();
        $result = $projects->list(ProjectQuery::fromValidated($validated));

        return Inertia::render('Projects/Index', [
            'projects' => ProjectResource::collection($result->data)->resolve(),
            'filters' => [
                'search' => $validated['search'] ?? '',
                'status' => $validated['status'] ?? '',
                'priority' => $validated['priority'] ?? '',
                'sort' => $validated['sort'] ?? ProjectQuery::DEFAULT_SORT,
                'direction' => $validated['direction'] ?? 'asc',
            ],
            'statuses' => ProjectStatus::values(),
            'priorities' => ProjectPriority::values(),
        ]);
    }
}
