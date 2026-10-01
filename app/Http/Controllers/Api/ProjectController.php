<?php

namespace App\Http\Controllers\Api;

use App\Data\ProjectPayload;
use App\Data\ProjectQuery;
use App\Data\ProjectResult;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\ListProjectsRequest;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class ProjectController extends Controller
{
    public function index(ListProjectsRequest $request, ProjectService $projects): JsonResponse
    {
        return $this->respond($projects->list(ProjectQuery::fromValidated($request->validated())));
    }

    public function show(int $id, ProjectService $projects): JsonResponse
    {
        return $this->respond($projects->find($id));
    }

    public function store(StoreProjectRequest $request, ProjectService $projects): JsonResponse
    {
        return $this->respond($projects->create(ProjectPayload::fromValidated($request->validated())));
    }

    public function update(UpdateProjectRequest $request, int $id, ProjectService $projects): JsonResponse
    {
        return $this->respond($projects->update($id, ProjectPayload::fromValidated($request->validated())));
    }

    public function destroy(int $id, ProjectService $projects): JsonResponse
    {
        return $this->respond($projects->delete($id));
    }

    private function respond(ProjectResult $result): JsonResponse
    {
        if (! $result->success) {
            return response()->json([
                'message' => $result->message,
                'errors' => $result->errors,
            ], $result->status);
        }

        if ($result->data === null) {
            return response()->json([
                'message' => $result->message,
            ], $result->status);
        }

        $resource = $result->data instanceof Collection
            ? ProjectResource::collection($result->data)
            : new ProjectResource($result->data);

        return $resource->response()->setStatusCode($result->status);
    }
}
