<?php

namespace App\Enums;

enum RelationshipType: string
{
    case SPOUSE = 'Spouse';
    case PARTNER = 'Partner';
    case PARENT = 'Parent';
    case CHILD = 'Child';
    case SIBLING = 'Sibling';
    case OTHER = 'Other';
}