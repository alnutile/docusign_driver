<?php

namespace AlNutile\DocusignDriver\Responses;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class CastSubmitters implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $context): mixed
    {
        $results = [];
        foreach ($value as $item) {
            $results[] = Submitter::from($item);
        }

        return $results;
    }
}
