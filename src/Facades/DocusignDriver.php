<?php

namespace AlNutile\DocusignDriver\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AlNutile\DocusignDriver\DocusignDriver
 */
class DocusignDriver extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \AlNutile\DocusignDriver\DocusignDriver::class;
    }
}
