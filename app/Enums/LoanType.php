<?php

namespace App\Enums;

enum LoanType: string
{
    case SSS_SALARY = 'SSS Salary Loan';
    case SSS_CALAMITY = 'SSS Calamity Loan';
    case PAGIBIG_MPL = 'Pag-IBIG Multi-Purpose Loan';
    case PAGIBIG_CALAMITY = 'Pag-IBIG Calamity Loan';
    case COMPANY = 'Company Loan';
}
