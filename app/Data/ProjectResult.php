<?php

namespace App\Data;

final class ProjectResult
{
    /**
     * @param  array<string, mixed>  $errors
     */
    private function __construct(
        public readonly bool $success,
        public readonly mixed $data,
        public readonly string $message,
        public readonly int $status,
        public readonly array $errors,
    ) {}

    public static function ok(mixed $data = null, string $message = '', int $status = 200): self
    {
        return new self(true, $data, $message, $status, []);
    }

    /**
     * @param  array<string, mixed>  $errors
     */
    public static function fail(string $message, int $status = 422, array $errors = []): self
    {
        return new self(false, null, $message, $status, $errors);
    }
}
