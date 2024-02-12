<?php

namespace AlNutile\DocusignDriver\Responses;

use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class TemplateDto extends Data
{
    /**
     * @param  FieldsDto[]  $fields
     * @return void
     */
    public function __construct(
        public string $id,
        public ?string $slug,
        public string $name,
        #[WithCast(FieldsCaster::class)]
        public ?array $fields,
    ) {

    }
}
