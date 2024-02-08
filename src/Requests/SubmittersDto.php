<?php

namespace AlNutile\DocusignDriver\Requests;

use Spatie\LaravelData\Data;

class SubmittersDto extends Data
{
    public function __construct(
        public ?string $email,
        public ?string $phone,
        public array $fields,
    ) {

    }
}
