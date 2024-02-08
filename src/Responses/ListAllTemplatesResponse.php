<?php

namespace AlNutile\DocusignDriver\Responses;

use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class ListAllTemplatesResponse extends Data
{
    /**
     * @param  TemplateDto[]  $templates
     * @return void
     */
    public function __construct(
        #[WithCast(TemplateCaster::class)]
        public array $templates
    ) {

    }
}
