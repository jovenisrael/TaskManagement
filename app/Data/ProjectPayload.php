<?php

namespace App\Data;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;

final class ProjectPayload
{
    public function __construct(
        public readonly string $clientName,
        public readonly string $projectName,
        public readonly ?string $description,
        public readonly ProjectStatus $status,
        public readonly ProjectPriority $priority,
        public readonly string $startDate,
        public readonly string $dueDate,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromValidated(array $data): self
    {
        return new self(
            clientName: $data['clientName'],
            projectName: $data['projectName'],
            description: $data['description'] ?? null,
            status: ProjectStatus::from($data['status']),
            priority: ProjectPriority::from($data['priority']),
            startDate: $data['startDate'],
            dueDate: $data['dueDate'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toAttributes(): array
    {
        return [
            'client_name' => $this->clientName,
            'project_name' => $this->projectName,
            'description' => $this->description,
            'status' => $this->status->value,
            'priority' => $this->priority->value,
            'start_date' => $this->startDate,
            'due_date' => $this->dueDate,
        ];
    }
}
