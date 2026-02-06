<?php

namespace App\Enums;

enum Position: string
{
    // Management
    case GENERAL_MANAGER = 'General Manager';
    case OPERATIONS_MANAGER = 'Operations Manager';
    case HR_MANAGER = 'HR Manager';
    case FINANCE_MANAGER = 'Finance Manager';
    case IT_MANAGER = 'IT Manager';

    // Supervisory
    case SUPERVISOR = 'Supervisor';
    case TEAM_LEAD = 'Team Lead';

    // Field / Security Staff
    case SECURITY_OFFICER = 'Security Officer';
    case SENIOR_SECURITY_OFFICER = 'Senior Security Officer';
    case ARMED_SECURITY_OFFICER = 'Armed Security Officer';
    case SECURITY_PATROL = 'Security Patrol';
    case CONTROL_ROOM_OFFICER = 'Control Room Officer';

    // Support / Admin
    case ADMIN_CLERK = 'Admin Clerk';
    case LOGISTICS_OFFICER = 'Logistics Officer';
    case TRAINER = 'Trainer';
    case RECRUITMENT_OFFICER = 'Recruitment Officer';
    case IT_SUPPORT = 'IT Support';
}
