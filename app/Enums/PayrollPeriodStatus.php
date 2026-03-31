<?php

namespace App\Enums;

enum PayrollPeriodStatus: string
{
    case DRAFT = 'Draft';
    case PROCESSING = 'Processing';
    case COMPLETED = 'Completed';
}
