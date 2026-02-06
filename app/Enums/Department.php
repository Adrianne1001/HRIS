<?php

namespace App\Enums;

enum Department: string
{
    case OPERATIONS = 'Operations';
    case ADMINISTRATION = 'Administration';
    case HUMAN_RESOURCES = 'Human Resources';
    case TRAINING = 'Training';
    case LOGISTICS = 'Logistics';
    case SECURITY_CONTROL = 'Security Control';
    case FINANCE = 'Finance';
    case IT = 'IT';
    case SALES_MARKETING = 'Sales & Marketing';
}
