# Design: Payroll & Compensation

## Overview

This design adds six new models (PayrollPeriod, PayrollRecord, DeductionType, PayrollDeduction, Holiday, EmployeeLoan), four new enums (PayrollPeriodStatus, PayrollPeriodType, HolidayType, LoanType), one service class (PayrollComputationService), four controllers, and associated views/routes. It integrates with Employee, WorkSchedule, AttendanceRecord, and LeaveRequest models to produce payroll computations aligned with Philippine labor law and tax regulations.

## New Enums

### PayrollPeriodStatus (`app/Enums/PayrollPeriodStatus.php`)

```php
enum PayrollPeriodStatus: string
{
    case DRAFT = 'Draft';
    case PROCESSING = 'Processing';
    case COMPLETED = 'Completed';
}
```

### PayrollPeriodType (`app/Enums/PayrollPeriodType.php`)

```php
enum PayrollPeriodType: string
{
    case FIRST_HALF = 'First Half';
    case SECOND_HALF = 'Second Half';
}
```

### HolidayType (`app/Enums/HolidayType.php`)

```php
enum HolidayType: string
{
    case REGULAR = 'Regular Holiday';
    case SPECIAL_NON_WORKING = 'Special Non-Working Day';
}
```

### LoanType (`app/Enums/LoanType.php`)

```php
enum LoanType: string
{
    case SSS_SALARY = 'SSS Salary Loan';
    case SSS_CALAMITY = 'SSS Calamity Loan';
    case PAGIBIG_MPL = 'Pag-IBIG Multi-Purpose Loan';
    case PAGIBIG_CALAMITY = 'Pag-IBIG Calamity Loan';
    case COMPANY = 'Company Loan';
}
```

## New Models

### PayrollPeriod (`app/Models/PayrollPeriod.php`)

**Table:** `payroll_periods`

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint (PK) | auto-increment |
| name | string | required (e.g., "March 1-15, 2026") |
| periodType | string | required (First Half / Second Half) |
| startDate | date | required |
| endDate | date | required |
| payDate | date | required |
| status | string | default 'Draft' |
| totalGrossPay | decimal(12,2) | default 0 |
| totalDeductions | decimal(12,2) | default 0 |
| totalNetPay | decimal(12,2) | default 0 |
| totalEmployerContributions | decimal(12,2) | default 0 |
| employeeCount | integer | default 0 |
| processedAt | datetime | nullable |
| completedAt | datetime | nullable |
| timestamps | | Laravel timestamps |
| System fields | | via MigrationHelper::addSystemFields |

**Relationships:**
- `hasMany` PayrollRecord (FK: payrollPeriodID)

**Constraints:**
- Unique composite: `[startDate, endDate]` (no duplicate periods)

**Casts:**
- `startDate` → `date`
- `endDate` → `date`
- `payDate` → `date`
- `periodType` → `PayrollPeriodType::class`
- `status` → `PayrollPeriodStatus::class`
- `totalGrossPay` → `decimal:2`
- `totalDeductions` → `decimal:2`
- `totalNetPay` → `decimal:2`
- `totalEmployerContributions` → `decimal:2`
- `processedAt` → `datetime`
- `completedAt` → `datetime`

### PayrollRecord (`app/Models/PayrollRecord.php`)

**Table:** `payroll_records`

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint (PK) | auto-increment |
| payrollPeriodID | bigint (FK) | references payroll_periods.id |
| employeeID | bigint (FK) | references employees.employeeID |
| basicMonthlySalary | decimal(12,2) | employee's salary at time of processing |
| dailyRate | decimal(10,2) | computed |
| hourlyRate | decimal(10,2) | computed |
| daysWorked | decimal(5,2) | from attendance |
| daysAbsent | decimal(5,2) | computed (AWOL only, excludes approved leave) |
| approvedLeaveDays | decimal(5,2) | from approved LeaveRequests in period |
| regularHoursWorked | decimal(7,2) | from attendance |
| overtimeHours | decimal(7,2) | total OT hours |
| nightDifferentialHours | decimal(7,2) | hours worked between 10 PM–6 AM |
| holidayDaysWorked | decimal(5,2) | regular holiday days worked in period |
| specialHolidayDaysWorked | decimal(5,2) | special non-working days worked in period |
| lateMinutes | decimal(7,2) | total late minutes in period |
| undertimeMinutes | decimal(7,2) | total undertime minutes in period |
| basicPay | decimal(12,2) | (basicMonthlySalary / 2) for semi-monthly |
| absentDeduction | decimal(12,2) | dailyRate × daysAbsent |
| lateDeduction | decimal(12,2) | hourlyRate × (lateMinutes / 60) |
| undertimeDeduction | decimal(12,2) | hourlyRate × (undertimeMinutes / 60) |
| overtimePay | decimal(12,2) | hourlyRate × OT hours × 1.25 |
| nightDifferentialPay | decimal(12,2) | hourlyRate × ND hours × 0.10 |
| holidayPay | decimal(12,2) | premium pay for regular holidays worked |
| specialHolidayPay | decimal(12,2) | premium pay for special non-working days worked |
| grossPay | decimal(12,2) | basicPay - absentDeduction - lateDeduction - undertimeDeduction + overtimePay + nightDifferentialPay + holidayPay + specialHolidayPay |
| sssEmployee | decimal(10,2) | SSS employee share (2nd cutoff only) |
| sssEmployer | decimal(10,2) | SSS employer share (tracked, not deducted) |
| sssEC | decimal(10,2) | Employees' Compensation (tracked, not deducted) |
| philhealthEmployee | decimal(10,2) | PhilHealth employee share (2nd cutoff only) |
| philhealthEmployer | decimal(10,2) | PhilHealth employer share (tracked, not deducted) |
| pagibigEmployee | decimal(10,2) | Pag-IBIG employee share (2nd cutoff only) |
| pagibigEmployer | decimal(10,2) | Pag-IBIG employer share (tracked, not deducted) |
| withholdingTax | decimal(10,2) | BIR withholding tax (2nd cutoff only) |
| totalMandatoryDeductions | decimal(12,2) | sssEmployee + philhealthEmployee + pagibigEmployee + withholdingTax |
| totalLoanDeductions | decimal(12,2) | sum of loan amortization deductions |
| totalDeductions | decimal(12,2) | totalMandatoryDeductions + totalLoanDeductions |
| netPay | decimal(12,2) | grossPay - totalDeductions |
| timestamps | | Laravel timestamps |
| System fields | | via MigrationHelper::addSystemFields |

**Relationships:**
- `belongsTo` PayrollPeriod (FK: payrollPeriodID)
- `belongsTo` Employee (FK: employeeID, owner key: employeeID)
- `hasMany` PayrollDeduction (FK: payrollRecordID)

**Constraints:**
- Unique composite: `[payrollPeriodID, employeeID]`

**Casts:**
- All decimal columns → `decimal:2`
- `CreatedDateTime` → `datetime`
- `LastModifiedDateTime` → `datetime`

### DeductionType (`app/Models/DeductionType.php`)

**Table:** `deduction_types`

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint (PK) | auto-increment |
| name | string | required (e.g., "SSS", "PhilHealth") |
| code | string | unique, required (e.g., "SSS", "PHIC", "HDMF", "TAX") |
| description | text | nullable |
| computationMethod | string | required: 'fixed', 'percentage', 'bracket' |
| isStatutory | boolean | default false |
| isActive | boolean | default true |
| fixedAmount | decimal(10,2) | nullable (for fixed method) |
| employeeRate | decimal(5,4) | nullable (employee percentage, e.g., 0.0250 for PhilHealth 2.5%) |
| employerRate | decimal(5,4) | nullable (employer percentage, e.g., 0.0250 for PhilHealth 2.5%) |
| bracketTable | json | nullable (for bracket-based lookups) |
| salaryFloor | decimal(12,2) | nullable (minimum salary for computation, e.g., PhilHealth ₱10,000) |
| salaryCeiling | decimal(12,2) | nullable (maximum salary for computation, e.g., PhilHealth ₱100,000) |
| maxEmployeeAmount | decimal(10,2) | nullable (cap on employee deduction per month, e.g., Pag-IBIG ₱200) |
| maxEmployerAmount | decimal(10,2) | nullable (cap on employer contribution per month) |
| timestamps | | Laravel timestamps |
| System fields | | via MigrationHelper::addSystemFields |

**Relationships:**
- `hasMany` PayrollDeduction (FK: deductionTypeID)

**bracketTable JSON structure** (SSS Monthly Salary Credit table):
```json
[
  { "min": 0, "max": 4249.99, "msc": 4000, "employeeShare": 180.00, "employerShare": 380.00, "ec": 10.00 },
  { "min": 4250, "max": 4749.99, "msc": 4500, "employeeShare": 202.50, "employerShare": 427.50, "ec": 10.00 }
]
```

For Pag-IBIG:
```json
[
  { "min": 0, "max": 1500, "employeeRate": 0.01, "employerRate": 0.02 },
  { "min": 1500.01, "max": null, "employeeRate": 0.02, "employerRate": 0.02 }
]
```

For BIR tax brackets (semi-monthly):
```json
[
  { "min": 0, "max": 10417, "base": 0, "rate": 0 },
  { "min": 10417, "max": 16667, "base": 0, "rate": 0.15 },
  { "min": 16667, "max": 33333, "base": 937.50, "rate": 0.20 },
  { "min": 33333, "max": 83333, "base": 4270.83, "rate": 0.25 },
  { "min": 83333, "max": 333333, "base": 16770.83, "rate": 0.30 },
  { "min": 333333, "max": null, "base": 91770.83, "rate": 0.35 }
]
```

### PayrollDeduction (`app/Models/PayrollDeduction.php`)

**Table:** `payroll_deductions`

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint (PK) | auto-increment |
| payrollRecordID | bigint (FK) | references payroll_records.id |
| deductionTypeID | bigint (FK) | nullable, references deduction_types.id |
| employeeLoanID | bigint (FK) | nullable, references employee_loans.id |
| description | string | required (e.g., "SSS Employee Share", "SSS Salary Loan") |
| employeeAmount | decimal(10,2) | amount deducted from employee |
| employerAmount | decimal(10,2) | default 0, employer share (tracked only) |
| remarks | text | nullable |
| timestamps | | Laravel timestamps |
| System fields | | via MigrationHelper::addSystemFields |

**Relationships:**
- `belongsTo` PayrollRecord (FK: payrollRecordID)
- `belongsTo` DeductionType (FK: deductionTypeID, nullable)
- `belongsTo` EmployeeLoan (FK: employeeLoanID, nullable)

**Notes:**
- Statutory deductions reference `deductionTypeID` and leave `employeeLoanID` null
- Loan deductions reference `employeeLoanID` and may leave `deductionTypeID` null
- `employerAmount` stores the employer's matching share for government contributions (not deducted from employee)

### Holiday (`app/Models/Holiday.php`)

**Table:** `holidays`

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint (PK) | auto-increment |
| name | string | required (e.g., "New Year's Day") |
| date | date | required |
| holidayType | string | required (Regular Holiday / Special Non-Working Day) |
| year | integer | required (for filtering/grouping) |
| timestamps | | Laravel timestamps |
| System fields | | via MigrationHelper::addSystemFields |

**Relationships:** None (standalone reference table)

**Casts:**
- `date` → `date`
- `holidayType` → `HolidayType::class`

**Constraints:**
- Unique composite: `[date, name]`

### EmployeeLoan (`app/Models/EmployeeLoan.php`)

**Table:** `employee_loans`

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint (PK) | auto-increment |
| employeeID | bigint (FK) | references employees.employeeID |
| loanType | string | required (LoanType enum) |
| referenceNbr | string | nullable (government loan reference number) |
| loanAmount | decimal(12,2) | required (original loan amount) |
| monthlyAmortization | decimal(10,2) | required (monthly deduction amount) |
| totalPaid | decimal(12,2) | default 0 |
| remainingBalance | decimal(12,2) | required (= loanAmount initially) |
| startDate | date | required |
| endDate | date | nullable |
| isActive | boolean | default true |
| remarks | text | nullable |
| timestamps | | Laravel timestamps |
| System fields | | via MigrationHelper::addSystemFields |

**Relationships:**
- `belongsTo` Employee (FK: employeeID, owner key: employeeID)
- `hasMany` PayrollDeduction (FK: employeeLoanID)

**Casts:**
- `loanType` → `LoanType::class`
- `loanAmount` → `decimal:2`
- `monthlyAmortization` → `decimal:2`
- `totalPaid` → `decimal:2`
- `remainingBalance` → `decimal:2`
- `startDate` → `date`
- `endDate` → `date`
- `isActive` → `boolean`

## Model Relationship Additions

### Employee (`app/Models/Employee.php`)
- Add `hasMany` PayrollRecord (FK: employeeID, local key: employeeID)
- Add `hasMany` EmployeeLoan (FK: employeeID, local key: employeeID)

## Service Class

### PayrollComputationService (`app/Services/PayrollComputationService.php`)

Central service for all payroll calculations, following Philippine payroll regulations.

**computePayrollForEmployee(Employee $employee, PayrollPeriod $period): array**
1. Get employee's attendance records within period date range
2. Get approved leave requests overlapping with period date range
3. Get holidays within period date range
4. Calculate working days in period (from employee's work schedule workingDays for dates in range)
5. Calculate daysWorked (count of attendance records with actualTimeIn)
6. Calculate approvedLeaveDays from approved LeaveRequests in the period
7. Calculate daysAbsent = working days − daysWorked − approvedLeaveDays (minimum 0)
8. Compute dailyRate = basicMonthlySalary / standardWorkingDaysPerMonth (derived from schedule's workingDays: count weekdays matching schedule in a month, default 22)
9. Compute hourlyRate = dailyRate / workSchedule.totalWorkHours
10. Compute basicPay = basicMonthlySalary / 2 (semi-monthly base)
11. Compute absentDeduction = dailyRate × daysAbsent
12. Sum regularHoursWorked from AttendanceRecord.hoursWorked
13. Sum overtimeHours = advanceOTHours + afterShiftOTHours from AttendanceRecords
14. Calculate lateMinutes by comparing actualTimeIn vs shiftTimeIn for each record where actualTimeIn > shiftTimeIn
15. Calculate undertimeMinutes by comparing actualTimeOut vs shiftTimeOut for each record where actualTimeOut < shiftTimeOut
16. Calculate nightDifferentialHours by checking overlap of actual work time with the 10 PM – 6 AM window for each attendance record
17. Identify attendance records falling on Regular Holidays → count holidayDaysWorked, compute holidayPay = dailyRate × holidayDaysWorked × 1.00 (100% premium for monthly-paid)
18. Identify attendance records falling on Special Non-Working Days → count specialHolidayDaysWorked, compute specialHolidayPay = dailyRate × specialHolidayDaysWorked × 0.30 (30% premium)
19. Compute overtimePay = hourlyRate × overtimeHours × 0.25 (25% premium — employee already receives base through basicPay)
20. Compute nightDifferentialPay = hourlyRate × nightDifferentialHours × 0.10
21. Compute lateDeduction = hourlyRate × (lateMinutes / 60)
22. Compute undertimeDeduction = hourlyRate × (undertimeMinutes / 60)
23. Compute grossPay = basicPay − absentDeduction − lateDeduction − undertimeDeduction + overtimePay + nightDifferentialPay + holidayPay + specialHolidayPay
24. Return associative array of all computed values

**computeMandatoryDeductions(PayrollRecord $record, PayrollPeriod $period): array**
Only applies on Second Half periods (per key decision #2). Returns zeros for First Half.

1. Get employee's basicMonthlySalary
2. **SSS**: Look up MSC bracket from SSS DeductionType bracketTable using basicMonthlySalary. Return employeeShare, employerShare, ec from matching bracket.
3. **PhilHealth**: Clamp salary between floor (₱10,000) and ceiling (₱100,000). Compute: clampedSalary × employeeRate (0.0250). Employee share = result. Employer share = same.
4. **Pag-IBIG**: Look up bracket from Pag-IBIG DeductionType bracketTable. Apply employee rate and employer rate to salary. Cap employee at maxEmployeeAmount (₱200/month), cap employer at maxEmployerAmount (₱200/month).
5. **Withholding Tax**: Compute semi-monthly taxable income = grossPay − sssEmployee − philhealthEmployee − pagibigEmployee. Look up BIR bracket: tax = base + rate × (taxableIncome − bracket.min). Minimum 0.
6. Return array of all mandatory deduction amounts (employee + employer shares).

**computeLoanDeductions(Employee $employee, PayrollPeriod $period): array**
1. Get all active EmployeeLoans for the employee where startDate <= period.endDate
2. For each loan: semiMonthlyAmount = monthlyAmortization / 2
3. If remainingBalance < semiMonthlyAmount, use remainingBalance instead
4. Return array of [employeeLoanID => deductionAmount]

**computeWithholdingTax(float $semiMonthlyTaxableIncome): float**
1. Load BIR tax bracket table from DeductionType where code = 'TAX'
2. Find matching bracket for semiMonthlyTaxableIncome
3. Compute tax = bracket.base + bracket.rate × (semiMonthlyTaxableIncome − bracket.min)
4. Return max(0, tax)

**calculateNightDifferentialHours(AttendanceRecord $record): float**
1. Define ND window: 10:00 PM to 6:00 AM next day
2. Calculate overlap between actual work hours (actualTimeIn to actualTimeOut) and the ND window
3. Return hours of overlap (decimal)

**calculateWorkingDaysInPeriod(Carbon $start, Carbon $end, string $workingDays): int**
1. Iterate each day between start and end dates (inclusive)
2. Count days that match the employee's workSchedule workingDays pattern
3. Return count

## Controllers

### PayrollPeriodController (`app/Http/Controllers/PayrollPeriodController.php`)

| Method | Route | Description |
|--------|-------|-------------|
| index | GET /payroll | List payroll periods with status filter, year filter |
| create | GET /payroll/create | Show create form |
| store | POST /payroll | Create payroll period (Draft status) |
| show | GET /payroll/{payrollPeriod} | Show period with payroll records summary |
| process | POST /payroll/{payrollPeriod}/process | Generate payroll records for all active employees |
| complete | POST /payroll/{payrollPeriod}/complete | Mark period as Completed |
| destroy | DELETE /payroll/{payrollPeriod} | Delete (only if Draft status) |

**store validation:**
- name: required, string, max:255
- periodType: required, in:First Half,Second Half
- startDate: required, date
- endDate: required, date, after:startDate
- payDate: required, date, after_or_equal:endDate

**process logic (inside DB::transaction):**
1. Validate period status is Draft
2. Set status to Processing, processedAt = now()
3. Get all active employees (employmentStatus = Active) with work schedules
4. For each employee:
   a. Call PayrollComputationService::computePayrollForEmployee()
   b. Create PayrollRecord with computed earnings values
   c. If period is Second Half: call computeMandatoryDeductions(), create PayrollDeduction entries for SSS, PhilHealth, Pag-IBIG, Tax with both employee and employer amounts
   d. Call computeLoanDeductions(), create PayrollDeduction entries for each active loan, update EmployeeLoan.remainingBalance and totalPaid
   e. Sum totalMandatoryDeductions, totalLoanDeductions, totalDeductions, and compute netPay on PayrollRecord
5. Update PayrollPeriod totals (totalGrossPay, totalDeductions, totalNetPay, totalEmployerContributions, employeeCount)

**complete logic:**
1. Validate period status is Processing
2. Set status to Completed, completedAt = now()

### PayslipController (`app/Http/Controllers/PayslipController.php`)

| Method | Route | Description |
|--------|-------|-------------|
| index | GET /payslips | List employee's own payslips (completed periods only) |
| show | GET /payslips/{payrollRecord} | Show detailed payslip |

**Authorization:** Employee can only view their own payslips (matched via auth user → employee → payroll records).

### EmployeeLoanController (`app/Http/Controllers/EmployeeLoanController.php`)

| Method | Route | Description |
|--------|-------|-------------|
| index | GET /employee-loans | List all employee loans with filters |
| create | GET /employee-loans/create | Show create form |
| store | POST /employee-loans | Create new loan record |
| show | GET /employee-loans/{employeeLoan} | Show loan details with deduction history |
| edit | GET /employee-loans/{employeeLoan}/edit | Edit loan details |
| update | PUT /employee-loans/{employeeLoan} | Update loan |
| destroy | DELETE /employee-loans/{employeeLoan} | Delete (only if no payroll deductions) |

**store/update validation:**
- employeeID: required, exists:employees,employeeID
- loanType: required, in:SSS Salary Loan,SSS Calamity Loan,Pag-IBIG Multi-Purpose Loan,Pag-IBIG Calamity Loan,Company Loan
- referenceNbr: nullable, string, max:100
- loanAmount: required, numeric, min:0
- monthlyAmortization: required, numeric, min:0
- startDate: required, date
- endDate: nullable, date, after:startDate
- remarks: nullable, string

### HolidayController (`app/Http/Controllers/HolidayController.php`)

| Method | Route | Description |
|--------|-------|-------------|
| index | GET /holidays | List holidays with year filter |
| create | GET /holidays/create | Show create form |
| store | POST /holidays | Create holiday |
| edit | GET /holidays/{holiday}/edit | Show edit form |
| update | PUT /holidays/{holiday} | Update holiday |
| destroy | DELETE /holidays/{holiday} | Delete holiday |

**store/update validation:**
- name: required, string, max:255
- date: required, date
- holidayType: required, in:Regular Holiday,Special Non-Working Day
- year: required, integer, min:2020

## Routes (`routes/web.php`)

Add inside existing `Route::middleware('auth')->group(...)`:

```php
// Payroll Management
Route::post('payroll/{payrollPeriod}/process', [PayrollPeriodController::class, 'process'])
    ->name('payroll.process');
Route::post('payroll/{payrollPeriod}/complete', [PayrollPeriodController::class, 'complete'])
    ->name('payroll.complete');
Route::resource('payroll', PayrollPeriodController::class)->except(['edit', 'update']);

// Payslips (employee self-service)
Route::get('payslips', [PayslipController::class, 'index'])->name('payslips.index');
Route::get('payslips/{payrollRecord}', [PayslipController::class, 'show'])->name('payslips.show');

// Employee Loans
Route::resource('employee-loans', EmployeeLoanController::class);

// Holidays
Route::resource('holidays', HolidayController::class)->except(['show']);
```

## Views

### Payroll Periods (`resources/views/payroll/`)
- **index.blade.php** — Data table: Period Name, Type badge (First Half/Second Half), Date Range, Pay Date, Status badge, Employee Count, Total Net Pay, Actions. Status filter tabs (All/Draft/Processing/Completed). Year filter dropdown.
- **create.blade.php** — Form: name, periodType dropdown (First Half / Second Half), startDate, endDate, payDate. Auto-suggest name from selected dates.
- **show.blade.php** — Period summary card: totals (gross, deductions, net, employer contributions), status, dates. Data table of payroll records: Employee Name, Basic Pay, OT Pay, ND Pay, Holiday Pay, Gross Pay, Mandatory Deductions, Loan Deductions, Net Pay. Action buttons: Process (if Draft), Complete (if Processing). Government contribution summary section: total SSS (employee + employer + EC), total PhilHealth (employee + employer), total Pag-IBIG (employee + employer) — for remittance reference.

### Payslips (`resources/views/payslips/`)
- **index.blade.php** — List of completed payroll periods for the employee: Period Name, Type, Pay Date, Net Pay, View link.
- **show.blade.php** — Full payslip layout:
  - Header: company name, payroll period, employee info (name, department, position)
  - Earnings section: Basic Pay, Overtime Pay, Night Differential Pay, Holiday Pay, Special Holiday Pay, less Absent/Late/Undertime deductions, Gross Pay
  - Attendance summary: Days Worked, Days Absent, Approved Leave Days, OT Hours, ND Hours, Late Minutes, Undertime Minutes
  - Mandatory deductions section (2nd cutoff only): SSS, PhilHealth, Pag-IBIG, Withholding Tax
  - Loan deductions section: each loan type + amount
  - Summary footer: Gross Pay, Total Deductions breakdown, Net Pay

### Employee Loans (`resources/views/employee-loans/`)
- **index.blade.php** — Data table: Employee Name, Loan Type badge, Ref #, Loan Amount, Monthly Amortization, Remaining Balance, Active/Inactive badge, Actions. Filters: employee search, loan type, active status.
- **create.blade.php** — Form: employeeID (searchable dropdown), loanType, referenceNbr, loanAmount, monthlyAmortization, startDate, endDate, remarks. remainingBalance auto-set to loanAmount.
- **edit.blade.php** — Same form pre-populated. Cannot edit employeeID or loanType after creation.
- **show.blade.php** — Loan summary card + deduction history table showing: payroll period, amount deducted, running balance after deduction.

### Holidays (`resources/views/holidays/`)
- **index.blade.php** — Data table: Name, Date, Type badge (Regular Holiday / Special Non-Working Day), Year. Year filter dropdown. Add Holiday button.
- **create.blade.php** — Form: name, date, holidayType dropdown, year (auto-filled from date).
- **edit.blade.php** — Same form pre-populated.

### Sidebar Navigation
Update existing "Payroll" section with links to: Payroll Periods (/payroll), My Payslips (/payslips), Employee Loans (/employee-loans), Holidays (/holidays).

## Database Seeders

### DeductionTypeSeeder

Seeds Philippine statutory deductions with 2026 rates:

| Name | Code | Method | Employee Rate | Employer Rate | Notes |
|------|------|--------|---------------|---------------|-------|
| SSS | SSS | bracket | 4.5% | 9.5% | bracketTable stores full 2026 MSC table with employeeShare, employerShare, ec per bracket. MSC range ₱4,000–₱30,000. |
| PhilHealth | PHIC | percentage | 2.5% (0.0250) | 2.5% (0.0250) | salaryFloor: 10000, salaryCeiling: 100000. Monthly premium = salary × 5%, split 50/50. |
| Pag-IBIG | HDMF | bracket | — | — | bracketTable: ≤₱1,500 → EE 1% ER 2%; >₱1,500 → EE 2% ER 2%. maxEmployeeAmount: 200, maxEmployerAmount: 200. |
| Withholding Tax | TAX | bracket | — | — | bracketTable stores BIR semi-monthly tax brackets per TRAIN Law (2023+). Six brackets from 0% to 35%. |

### HolidaySeeder

Seeds 2026 Philippine holidays:

**Regular Holidays:**
- New Year's Day (Jan 1)
- Araw ng Kagitingan (Apr 9)
- Maundy Thursday (Apr 2)
- Good Friday (Apr 3)
- Labor Day (May 1)
- Independence Day (Jun 12)
- National Heroes Day (Aug 31)
- Bonifacio Day (Nov 30)
- Christmas Day (Dec 25)
- Rizal Day (Dec 30)

**Special Non-Working Days:**
- Ninoy Aquino Day (Aug 21)
- All Saints' Day (Nov 1)
- Feast of the Immaculate Conception (Dec 8)
- Last Day of the Year (Dec 31)
- Chinese New Year (Feb 17)
- EDSA People Power Anniversary (Feb 25)
- Black Saturday (Apr 4)

### DatabaseSeeder update
Add DeductionTypeSeeder and HolidaySeeder after existing seeders.

## Factories

- **PayrollPeriodFactory** — name, periodType (random First Half/Second Half), date range, payDate, status = Draft
- **PayrollRecordFactory** — PayrollPeriod + Employee factories, salary/rate/hours/pay defaults, all mandatory deduction fields default 0
- **DeductionTypeFactory** — name, code, computationMethod = fixed, isStatutory = false
- **PayrollDeductionFactory** — PayrollRecord factory, employeeAmount, employerAmount = 0, description
- **HolidayFactory** — name, date, holidayType (random), year from date
- **EmployeeLoanFactory** — Employee factory, loanType (random), loanAmount between 5000–50000, monthlyAmortization, remainingBalance = loanAmount, isActive = true

## Testing Strategy

### PayrollPeriod Tests
- CRUD operations and validation
- Status transitions (Draft → Processing → Completed)
- Cannot process non-Draft period
- Cannot complete non-Processing period
- Cannot delete non-Draft period
- Process generates records for all active employees
- Period totals are correctly summed

### PayrollComputationService Tests
- Daily rate and hourly rate calculation from salary and schedule
- Basic pay = basicMonthlySalary / 2
- Absent deduction correctly excludes approved leave days
- OT pay at 125% (25% premium)
- Night differential at 10% of hourly rate
- Holiday pay: Regular Holiday 100% premium, Special Non-Working Day 30% premium
- Late/undertime deduction computation
- Gross pay formula verification
- SSS bracket lookup returns correct employee/employer/EC shares
- PhilHealth computation with floor ₱10,000 and ceiling ₱100,000
- PhilHealth 50/50 split (employee 2.5%, employer 2.5%)
- Pag-IBIG bracket: 1% for ≤₱1,500, 2% for >₱1,500, capped at ₱200
- BIR withholding tax table lookup across all brackets
- Tax computed on income after mandatory contribution deductions
- Mandatory deductions only on Second Half periods (zero on First Half)
- Loan deduction: monthlyAmortization / 2 per semi-monthly period
- Loan deduction capped at remaining balance
- Loan remaining balance updated after deduction

### Payslip Tests
- Employee can view own payslips index
- Employee can view own payslip detail with full breakdown
- Employee cannot view another employee's payslip (403)
- Only completed period payslips shown in index
- First Half payslip shows no mandatory deductions section
- Second Half payslip shows all mandatory deductions

### EmployeeLoan Tests
- CRUD operations and validation
- Loan balance tracking (totalPaid + remainingBalance = loanAmount)
- Cannot delete loan with existing payroll deductions
- Deduction history displayed on show page
- Loan marked inactive when fully paid (remainingBalance = 0)

### Holiday Tests
- CRUD operations and validation
- Year filtering
- Holiday type display
- Duplicate date+name prevented
