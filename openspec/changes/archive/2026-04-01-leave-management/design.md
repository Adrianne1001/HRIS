# Design: Leave Management

## Overview

This design adds three new models (LeaveType, LeaveBalance, LeaveRequest), two new enums (LeaveStatus, HalfDayPeriod), three controllers, and associated views/routes to the existing HRIS application. It integrates with the existing Employee and AttendanceRecord models and follows all established conventions (camelCase columns, PascalCase system fields, HasSystemFields trait, MigrationHelper, inline validation, Pest tests).

## New Enums

### LeaveStatus (`app/Enums/LeaveStatus.php`)

```php
enum LeaveStatus: string
{
    case PENDING = 'Pending';
    case APPROVED = 'Approved';
    case REJECTED = 'Rejected';
    case CANCELLED = 'Cancelled';
}
```

### HalfDayPeriod (`app/Enums/HalfDayPeriod.php`)

```php
enum HalfDayPeriod: string
{
    case AM = 'AM';
    case PM = 'PM';
}
```

## New Models

### LeaveType (`app/Models/LeaveType.php`)

**Table:** `leave_types`

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint (PK) | auto-increment |
| name | string | required |
| code | string | unique, required |
| defaultCredits | decimal(5,2) | required |
| description | text | nullable |
| isActive | boolean | default true |
| isPaid | boolean | default true |
| requiresDocument | boolean | default false |
| maxConsecutiveDays | integer | nullable |
| gender | string | nullable (for gender-restricted types) |
| timestamps | | Laravel timestamps |
| System fields | | via MigrationHelper::addSystemFields |

**Relationships:**
- `hasMany` LeaveBalance (FK: leaveTypeID)
- `hasMany` LeaveRequest (FK: leaveTypeID)

**Casts:**
- `defaultCredits` → `decimal:2`
- `isActive` → `boolean`
- `isPaid` → `boolean`
- `requiresDocument` → `boolean`
- `gender` → `Gender::class` (nullable)

### LeaveBalance (`app/Models/LeaveBalance.php`)

**Table:** `leave_balances`

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint (PK) | auto-increment |
| employeeID | bigint (FK) | references employees.employeeID |
| leaveTypeID | bigint (FK) | references leave_types.id |
| year | integer | required |
| totalCredits | decimal(5,2) | required |
| usedCredits | decimal(5,2) | default 0 |
| pendingCredits | decimal(5,2) | default 0 |
| timestamps | | Laravel timestamps |
| System fields | | via MigrationHelper::addSystemFields |

**Constraints:**
- Unique composite: `[employeeID, leaveTypeID, year]`

**Relationships:**
- `belongsTo` Employee (FK: employeeID)
- `belongsTo` LeaveType (FK: leaveTypeID)

**Accessors:**
- `remainingCredits`: computed as `totalCredits - usedCredits - pendingCredits`

**Casts:**
- `totalCredits` → `decimal:2`
- `usedCredits` → `decimal:2`
- `pendingCredits` → `decimal:2`

### LeaveRequest (`app/Models/LeaveRequest.php`)

**Table:** `leave_requests`

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint (PK) | auto-increment |
| employeeID | bigint (FK) | references employees.employeeID |
| leaveTypeID | bigint (FK) | references leave_types.id |
| startDate | date | required |
| endDate | date | required |
| totalDays | decimal(5,2) | required |
| isHalfDay | boolean | default false |
| halfDayPeriod | string | nullable (AM/PM) |
| reason | text | required |
| status | string | default 'Pending' |
| approvedByID | bigint (FK) | nullable, references users.id |
| approvedAt | datetime | nullable |
| rejectionReason | text | nullable |
| timestamps | | Laravel timestamps |
| System fields | | via MigrationHelper::addSystemFields |

**Relationships:**
- `belongsTo` Employee (FK: employeeID)
- `belongsTo` LeaveType (FK: leaveTypeID)
- `belongsTo` User as `approvedBy` (FK: approvedByID)

**Casts:**
- `startDate` → `date`
- `endDate` → `date`
- `totalDays` → `decimal:2`
- `isHalfDay` → `boolean`
- `halfDayPeriod` → `HalfDayPeriod::class`
- `status` → `LeaveStatus::class`
- `approvedAt` → `datetime`

## Model Relationship Additions

### Employee (`app/Models/Employee.php`)

Add relationships:
- `hasMany` LeaveBalance (FK: employeeID, local key: employeeID)
- `hasMany` LeaveRequest (FK: employeeID, local key: employeeID)

## Controllers

### LeaveTypeController (`app/Http/Controllers/LeaveTypeController.php`)

| Method | Route | Description |
|--------|-------|-------------|
| index | GET /leave-types | List leave types with search and pagination |
| create | GET /leave-types/create | Show create form |
| store | POST /leave-types | Create leave type with inline validation |
| show | GET /leave-types/{leaveType} | Show leave type details |
| edit | GET /leave-types/{leaveType}/edit | Show edit form |
| update | PUT /leave-types/{leaveType} | Update leave type with inline validation |
| destroy | DELETE /leave-types/{leaveType} | Delete (only if no associated requests) |

**Validation (store/update):**
- name: required, string, max:255
- code: required, string, max:50, unique:leave_types (ignore on update)
- defaultCredits: required, numeric, min:0, max:999.99
- description: nullable, string
- isActive: boolean
- isPaid: boolean
- requiresDocument: boolean
- maxConsecutiveDays: nullable, integer, min:1
- gender: nullable, in:Male,Female

### LeaveRequestController (`app/Http/Controllers/LeaveRequestController.php`)

| Method | Route | Description |
|--------|-------|-------------|
| index | GET /leave-requests | List requests with status filter tabs, search, date range |
| create | GET /leave-requests/create | Show submission form with balance info |
| store | POST /leave-requests | Submit request with balance/overlap validation |
| show | GET /leave-requests/{leaveRequest} | Show request details with action buttons |
| approve | POST /leave-requests/{leaveRequest}/approve | Approve and create attendance records |
| reject | POST /leave-requests/{leaveRequest}/reject | Reject with reason |
| cancel | POST /leave-requests/{leaveRequest}/cancel | Cancel own pending request |

**store logic (inside DB::transaction):**
1. Validate inputs (leaveTypeID, startDate, endDate, isHalfDay, halfDayPeriod, reason)
2. Validate leave type is active and employee is eligible (gender check)
3. Calculate totalDays (business days between dates, or 0.5 for half-day)
4. Check for overlapping non-cancelled requests for same employee
5. Get or fail LeaveBalance for employee + type + current year
6. Validate remainingCredits >= totalDays
7. Create LeaveRequest with status = Pending
8. Increment pendingCredits on LeaveBalance

**approve logic (inside DB::transaction):**
1. Validate request is Pending
2. Update status to Approved, set approvedByID and approvedAt
3. On LeaveBalance: subtract totalDays from pendingCredits, add to usedCredits
4. Create AttendanceRecord entries for each business day in the leave period:
   - employee_id = request's employeeID
   - workDate = each leave day
   - remarks = mapped from leave type (VL→Vacation Leave, SL→Sick Leave, default→Vacation Leave)
   - shiftTimeIn/shiftTimeOut from employee's work schedule
   - hoursWorked = 0, actualTimeIn = null, actualTimeOut = null

**reject logic (inside DB::transaction):**
1. Validate request is Pending
2. Validate rejectionReason is provided
3. Update status to Rejected, set approvedByID, approvedAt, rejectionReason
4. Subtract totalDays from pendingCredits on LeaveBalance
