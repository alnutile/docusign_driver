<?php

namespace AlNutile\DocusignDriver\Responses;

use Spatie\LaravelData\Data;

class SubmissionResponse extends Data
{
    public function __construct(
        public int $id,
        public int $submission_id,
        public string $uuid,
        public string $email,
        public string $slug,
        public string $sent_at,
        public ?string $name,
        public string $phone,
        public ?string $application_key,
        public array $values
    ) {

    }
}
