<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum OutputType: string
{
    use HasValues;

    case PDF = 'pdf';
    case XML = 'xml';
}
