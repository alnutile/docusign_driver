<?php

namespace AlNutile\DocusignDriver\Responses;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class TemplateCaster implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $context): mixed
    {
        $results = [];
        foreach ($value as $template) {
            $results[] = TemplateDto::from([
                'id' => $template['templateId'],
                'slug' => $template['uri'],
                'name' => $template['name'],
                'fields' => [],
            ],
            );
        }

        return $results;
    }
}
