<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum GatewayType: string
{
    use HasValues;

    case VAULT = 'Vault';
    case ASSOCIATE = 'Associate';
}
