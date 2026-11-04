<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum BondStatus: string
{
    use HasValues;

    case PORTFOLIO = 'portfolio';
    case IN_BANK = 'in_bank';
    case PLEDGED = 'pledged';
    case CLEARED = 'cleared';
    case ENDORSED = 'endorsed';
    case DUD = 'dud';
    case RETURNED = 'returned';
    case PENDING = 'pending';
    case PAID = 'paid';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
}
