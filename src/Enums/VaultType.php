<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum VaultType: string
{
    use HasValues;

    case BANK_ACCOUNT = 'bank_account';
    case SAFE_DEPOSIT = 'safe_deposit';
}
