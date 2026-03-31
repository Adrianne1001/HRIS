<?php

namespace App\Enums;

enum HolidayType: string
{
    case REGULAR = 'Regular Holiday';
    case SPECIAL_NON_WORKING = 'Special Non-Working Day';
}
