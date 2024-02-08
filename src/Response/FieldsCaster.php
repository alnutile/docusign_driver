<?php

namespace AlNutile\DocusignDriver\Responses;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class FieldsCaster implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $context): mixed
    {
        $results = [];
        foreach ($value as $field) {
            $results[] = FieldsDto::from($field);
        }

        return $results;
    }
}
