<?php

namespace App\Http\Resources;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'clientName' => $this->client_name,
            'projectName' => $this->project_name,
            'description' => $this->description,
            'status' => $this->status instanceof ProjectStatus ? $this->status->value : $this->status,
            'priority' => $this->priority instanceof ProjectPriority ? $this->priority->value : $this->priority,
            'startDate' => $this->start_date?->toDateString(),
            'dueDate' => $this->due_date?->toDateString(),
        ];
    }
}
