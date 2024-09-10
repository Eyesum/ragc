<?php

namespace App\Enums;

enum MembershipStatus: string
{
    case ACTIVE = 'Active';
    case INACTIVE = 'Inactive';
    case CANCELLED = 'Cancelled';
    case OVERDUE = 'Overdue';
}