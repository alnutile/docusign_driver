<?php

namespace AlNutile\DocusignDriver\Responses;

use Spatie\LaravelData\Data;

class Submitter extends Data
{
    public function __construct(
        public int $id,
        public ?string $submission_id,
        public ?string $uuid,
        public string $email,
        public ?string $slug,
        public string $sent_at,
        public array $values,
        public ?string $completed_at,
        public ?string $name,
        public ?string $phone,
        public ?string $status,
        public ?string $application_key,
        public ?array $template,
        public ?array $documents
    ) {

    }
}
