<?php

namespace App\Enums;

enum PaymentPeriod: string
{
    case LIFETIME = 'Lifetime';
    case ANNUAL = 'Annual';
    case QUARTERLY = 'Quarterly';
    case MONTHLY = 'Monthly';
}