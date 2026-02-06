<?php

namespace App\Enums;

enum EmploymentType: string
{
    case REGULAR = 'Regular';
    case PROBATIONARY = 'Probationary';
    case CASUAL = 'Casual';
    case CONTRACTUAL = 'Contractual';
    case PROJECT_BASED = 'Project-based';
    case TEMPORARY = 'Temporary';
}
