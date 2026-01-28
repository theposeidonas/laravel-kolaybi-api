<?php

namespace Theposeidonas\Kolaybi\Enums;

enum AssociateType: string
{
    case FREELANCER = 'freelancer_employee';
    case FULL_TIME = 'full_time_employee';
    case PART_TIME = 'part_time_employee';
    case PARTNER = 'partner';
    case CUSTOMER = 'customer';
    case SUPPLIER = 'supplier';
    case VENDOR = 'vendor';
    case ABROAD = 'abroad';
    case BROKER = 'broker';
    case PUBLIC = 'public_enterprise';
    case CARRIER = 'carrier';
    case STUDENT = 'student';
    case SUBCONTRACTOR = 'subcontractor';
    case POTENTIAL = 'potential_customer';
    case SHIPPING = 'shipping_company';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
