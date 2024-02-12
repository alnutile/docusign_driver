<?php

namespace AlNutile\DocusignDriver\Responses;

use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class GetSubmissionResponse extends Data
{
    /**
     * @param  Submitter[]  $submitters
     */
    public function __construct(
        public int $id,
        public string $source,
        public ?string $audit_log_url,
        #[WithCast(CastSubmitters::class)]
        public array $submitters,
        public TemplateDto $template,
        public array $submission_events,
    ) {

    }
}
