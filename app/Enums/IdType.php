<?php

namespace App\Enums;

enum IdType: string
{
    case DRIVING_LICENSE = 'Driving License';
    case PASSPORT = 'Passport';
    case OTHER = 'Other';
}