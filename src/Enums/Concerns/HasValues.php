<?php

namespace Theposeidonas\Kolaybi\Enums\Concerns;

trait HasValues
{
    /**
     * @return list<string|int>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
