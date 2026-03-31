# Proposal: Leave Management

## Problem

FortiTech HRIS currently has no way to manage employee leave. While the AttendanceRecord model includes remarks for "Vacation Leave" and "Sick Leave," there is no underlying leave system — no leave types, no balance tracking, no request/approval workflow, and no way for employees to formally request time off. Supervisors have no visibility into team availability, and HR cannot track leave usage across the organization.

This forces leave tracking into manual processes (spreadsheets, paper forms), leading to:
- No real-time visibility into leave balances
- No audit trail for leave approvals
- Risk of overlapping absences going unnoticed
- No integration between approved leave and attendance records
- Compliance risk with Philippine labor law leave entitlements

## Solution

Build a complete Leave Management module that provides:

1. **Leave Types** — Configurable leave categories (Vacation, Sick, Emergency, Maternity/Paternity, Bereavement, Solo Parent) with default annual credits, eligibility rules, and policy settings (paid/unpaid, document requirements, consecutive day limits).

2. **Leave Balance Tracking** — Per-employee, per-year balances for each leave type, tracking total credits, used credits, and pending credits (from unapproved requests). Balances reset annually with bulk allocation support.

3. **Leave Request Workflow** — Employees submit leave requests specifying type, date range, half-day option, and reason. The system validates against available balance and checks for overlapping requests. Requests follow a Pending → Approved/Rejected/Cancelled lifecycle.

4. **Approval Workflow** — Any authenticated user can approve or reject pending leave requests (since no role-based access control exists yet). Rejection requires a reason. The system is designed for future role-based restrictions.

5. **Attendance Integration** — When a leave request is approved, the system automatically creates AttendanceRecord entries for each leave day with the appropriate remarks (Vacation Leave, Sick Leave, etc.), ensuring attendance records stay consistent.

6. **Leave History & Reporting** — Filterable views of all leave requests by status, employee, type, and date range. Self-service balance view for employees and admin-level balance management view.

## Scope

### In Scope
- Leave type CRUD with configurable credits, policies, and gender eligibility
- Leave balance management with annual allocation and bulk assign
- Leave request submission with balance validation and overlap detection
- Half-day leave support (AM/PM periods)
- Approval/rejection/cancellation workflow
- Automatic AttendanceRecord creation on approval
- Leave request listing with status filter tabs
- Self-service leave balance dashboard for employees
- Admin balance management and bulk allocation
- Sidebar navigation integration
- Pest feature tests for all workflows
- Database seeders with Philippine standard leave types

### Non-Goals
- Role-based access control (planned as a separate future change)
- Leave accrual rules (e.g., monthly accrual, prorated for mid-year hires) — start with simple annual allocation
- Calendar integration with external systems (Google Calendar, Outlook)
- Leave carry-over between years
- Compensatory/offset leave
- Holiday calendar integration
- Email/notification system for leave events
- File attachment uploads for supporting documents

## Key Decisions

1. **Annual reset model** — Leave balances are tracked per year and reset annually. No carry-over between years in this iteration.
2. **Half-day support** — Leave requests can be for half a day (AM or PM period), counted as 0.5 days against the balance.
3. **No overlapping requests** — The system prevents submitting a leave request that overlaps with an existing non-cancelled request for the same employee.
4. **Pending credits** — When a request is submitted, the requested days are reserved as "pending credits" on the balance, preventing over-booking even before approval.
5. **Auto-create attendance records** — Approved leave automatically generates AttendanceRecord entries for each leave day, using the existing remarks enum values (Vacation Leave, Sick Leave). For leave types without a matching remark, the system will use the closest appropriate remark.
6. **Business days calculation** — totalDays counts only weekdays (Monday–Friday) between start and end dates, unless it's a half-day request (which is always 0.5).
7. **Gender-restricted leave types** — Maternity and Paternity leave types are restricted by gender field, validated against the employee's gender on file.
8. **Soft delete prevention** — Leave types with associated requests cannot be deleted, only deactivated (isActive = false).
9. **No roles yet** — All authenticated users can access all leave management features. The UI and routes are structured to support future role-based restrictions without refactoring.
