<?php

namespace App\Data;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;

final class ProjectQuery
{
    public const DEFAULT_SORT = 'dueDate';

    /**
     * @var list<string>
     */
    public const SORTS = [
        'clientName',
        'projectName',
        'status',
        'priority',
        'startDate',
        'dueDate',
    ];

    public function __construct(
        public readonly ?string $search,
        public readonly ?ProjectStatus $status,
        public readonly ?ProjectPriority $priority,
        public readonly string $sort,
        public readonly string $direction,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromValidated(array $data): self
    {
        $sort = $data['sort'] ?? self::DEFAULT_SORT;

        return new self(
            search: filled($data['search'] ?? null) ? $data['search'] : null,
            status: filled($data['status'] ?? null) ? ProjectStatus::from($data['status']) : null,
            priority: filled($data['priority'] ?? null) ? ProjectPriority::from($data['priority']) : null,
            sort: in_array($sort, self::SORTS, true) ? $sort : self::DEFAULT_SORT,
            direction: ($data['direction'] ?? 'asc') === 'desc' ? 'desc' : 'asc',
        );
    }
}
