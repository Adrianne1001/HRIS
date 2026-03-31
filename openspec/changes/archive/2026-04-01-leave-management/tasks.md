# Tasks: leave-management

## Task 1: Create LeaveStatus and HalfDayPeriod enums
- **Agent**: model
- **Files**: app/Enums/LeaveStatus.php, app/Enums/HalfDayPeriod.php
- Create PHP 8.4 string-backed enum `LeaveStatus` with cases: PENDING = 'Pending', APPROVED = 'Approved', REJECTED = 'Rejected', CANCELLED = 'Cancelled'
- Create PHP 8.4 string-backed enum `HalfDayPeriod` with cases: AM = 'AM', PM = 'PM'
- Follow existing enum pattern in app/Enums/Gender.php

## Task 2: Create leave_types migration
- **Agent**: migration
- **Files**: database/migrations/2026_04_01_000001_create_leave_types_table.php
- Create leave_types table with: id (auto PK), name (string), code (string unique), defaultCredits (decimal:5,2), description (text nullable), isActive (boolean default true), isPaid (boolean default true), requiresDocument (boolean default false), maxConsecutiveDays (integer nullable), gender (string nullable), timestamps
- Call MigrationHelper::addSystemFields($table) at end of schema

## Task 3: Create leave_balances migration
- **Agent**: migration
- **Files**: database/migrations/2026_04_01_000002_create_leave_balances_table.php
- Create leave_balances table with: id (auto PK), employeeID (FK to employees.employeeID — use unsignedBigInteger + manual foreign key since it references a custom PK), leaveTypeID (foreignId constrained to leave_types.id), year (integer), totalCredits (decimal:5,2), usedCredits (decimal:5,2 default 0), pendingCredits (decimal:5,2 default 0), timestamps
- Call MigrationHelper::addSystemFields($table)
- Add unique constraint on [employeeID, leaveTypeID, year]

## Task 4: Create leave_requests migration
- **Agent**: migration
- **Files**: database/migrations/2026_04_01_000003_create_leave_requests_table.php
- Create leave_requests table with: id (auto PK), employeeID (FK to employees.employeeID — same pattern as Task 3), leaveTypeID (foreignId constrained to leave_types.id), startDate (date), endDate (date), totalDays (decimal:5,2), isHalfDay (boolean default false), halfDayPeriod (string nullable), reason (text), status (string default 'Pending'), approvedByID (foreignId nullable constrained to users.id nullOnDelete), approvedAt (datetime nullable), rejectionReason (text nullable), timestamps
- Call MigrationHelper::addSystemFields($table)

## Task 5: Create LeaveType model
- **Agent**: model
- **Files**: app/Models/LeaveType.php
- Use HasFactory, HasSystemFields traits
- fillable: name, code, defaultCredits, description, isActive, isPaid, requiresDocument, maxConsecutiveDays, gender
- Casts: defaultCredits → decimal:2, isActive → boolean, isPaid → boolean, requiresDocument → boolean, gender → Gender::class (nullable), CreatedDateTime → datetime, LastModifiedDateTime → datetime
- Relationships: hasMany LeaveBalance (FK leaveTypeID), hasMany LeaveRequest (FK leaveTypeID)

## Task 6: Create LeaveBalance model
- **Agent**: model
- **Files**: app/Models/LeaveBalance.php
- Use HasFactory, HasSystemFields traits
- fillable: employeeID, leaveTypeID, year, totalCredits, usedCredits, pendingCredits
- Casts: totalCredits → decimal:2, usedCredits → decimal:2, pendingCredits → decimal:2, CreatedDateTime → datetime, LastModifiedDateTime → datetime
- Relationships: belongsTo Employee (FK employeeID, owner key employeeID), belongsTo LeaveType (FK leaveTypeID)
- Accessor: getRemainingCreditsAttribute = totalCredits - usedCredits - pendingCredits

## Task 7: Create LeaveRequest model
- **Agent**: model
- **Files**: app/Models/LeaveRequest.php
- Use HasFactory, HasSystemFields traits
- fillable: employeeID, leaveTypeID, startDate, endDate, totalDays, isHalfDay, halfDayPeriod, reason, status, approvedByID, approvedAt, rejectionReason
- Casts: startDate → date, endDate → date, totalDays → decimal:2, isHalfDay → boolean, halfDayPeriod → HalfDayPeriod::class, status → LeaveStatus::class, approvedAt → datetime, CreatedDateTime → datetime, LastModifiedDateTime → datetime
- Relationships: belongsTo Employee (FK employeeID, owner key employeeID), belongsTo LeaveType (FK leaveTypeID), belongsTo User as approvedBy (FK approvedByID)

## Task 8: Add leave relationships to Employee model
- **Agent**: model
- **Files**: app/Models/Employee.php
- Add hasMany relationship: leaveBalances() → hasMany(LeaveBalance::class, 'employeeID', 'employeeID')
- Add hasMany relationship: leaveRequests() → hasMany(LeaveRequest::class, 'employeeID', 'employeeID')

## Task 9: Create LeaveType factory and seeder
- **Agent**: model
- **Files**: database/factories/LeaveTypeFactory.php, database/seeders/LeaveTypeSeeder.php
- Factory with standard defaults
- Seeder creates Philippine standard leave types: Vacation Leave (VL, 15 credits), Sick Leave (SL, 15 credits, requiresDocument), Emergency Leave (EL, 3 credits), Maternity Leave (ML, 105 credits, Female, requiresDocument), Paternity Leave (PL, 7 credits, Male, requiresDocument), Bereavement Leave (BL, 3 credits, requiresDocument), Solo Parent Leave (SPL, 7 credits, requiresDocument)

## Task 10: Create LeaveBalance factory and seeder
- **Agent**: model
- **Files**: database/factories/LeaveBalanceFactory.php, database/seeders/LeaveBalanceSeeder.php
- Factory with Employee::factory(), LeaveType::factory(), year = current, credits defaults
- Seeder: iterate active employees + active leave types, check gender eligibility, create LeaveBalance for year 2026 with totalCredits = defaultCredits

## Task 11: Create LeaveRequest factory
- **Agent**: model
- **Files**: database/factories/LeaveRequestFactory.php
- Factory with Employee::factory(), LeaveType::factory(), date range, reason, status = Pending

## Task 12: Update DatabaseSeeder
- **Agent**: model
- **Files**: database/seeders/DatabaseSeeder.php
- Add LeaveTypeSeeder and LeaveBalanceSeeder after existing EmployeeSeeder call

## Task 13: Create LeaveTypeController
- **Agent**: controller
- **Files**: app/Http/Controllers/LeaveTypeController.php
- Full CRUD with inline validation, search, pagination, delete prevention if requests exist

## Task 14: Create LeaveRequestController
- **Agent**: controller
- **Files**: app/Http/Controllers/LeaveRequestController.php
- index with status filter tabs, search, date range; create with balance info; store with balance/overlap validation in DB::transaction; show; approve (update balance + create AttendanceRecords) in DB::transaction; reject with reason in DB::transaction; cancel in DB::transaction

## Task 15: Create LeaveBalanceController
- **Agent**: controller
- **Files**: app/Http/Controllers/LeaveBalanceController.php
- index (personal balances), manage (admin all-employee view), allocate (bulk allocate in DB::transaction)

## Task 16: Create leave management routes
- **Agent**: routing
- **Files**: routes/web.php
- Add resource routes for leave-types and leave-requests, custom POST routes for approve/reject/cancel, GET/POST routes for leave-balances

## Task 17: Create leave-types Blade views
- **Agent**: ui-ux
- **Files**: resources/views/leave-types/index.blade.php, create.blade.php, edit.blade.php, show.blade.php
- Follow existing patterns: x-app-layout, semantic CSS classes, card layout, data-table, form-grid

## Task 18: Create leave-requests Blade views
- **Agent**: ui-ux
- **Files**: resources/views/leave-requests/index.blade.php, create.blade.php, show.blade.php
- Status filter tabs, data table, create form with balance display and Alpine.js half-day toggle, show with contextual action buttons

## Task 19: Create leave-balances Blade views
- **Agent**: ui-ux
- **Files**: resources/views/leave-balances/index.blade.php, manage.blade.php
- Self-service balance cards with progress bars, admin management table with bulk allocate

## Task 20: Add leave management to sidebar navigation
- **Agent**: ui-ux
- **Files**: resources/views/layouts/sidebar.blade.php
- Add Leave Management section with links: Leave Requests, My Balances, Leave Types, Manage Balances

## Task 21: Create Pest tests for LeaveType CRUD
- **Agent**: testing
- **Files**: tests/Feature/LeaveTypeTest.php
- Test CRUD operations, validation, delete prevention with associated requests

## Task 22: Create Pest tests for LeaveRequest workflows
- **Agent**: testing
- **Files**: tests/Feature/LeaveRequestTest.php
- Test create with balance validation, overlap prevention, approve/reject/cancel workflows, balance recalculation, AttendanceRecord creation on approval

## Task 23: Create Pest tests for LeaveBalance
- **Agent**: testing
- **Files**: tests/Feature/LeaveBalanceTest.php
- Test balance viewing, bulk allocation, gender eligibility, remaining credits computation
