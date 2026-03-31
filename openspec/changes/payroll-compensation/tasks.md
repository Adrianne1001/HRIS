# Tasks: payroll-compensation

## Task 1: Create PayrollPeriodStatus enum
- **Agent**: model
- **Files**: app/Enums/PayrollPeriodStatus.php
- Create PHP 8.4 string-backed enum with cases: DRAFT = 'Draft', PROCESSING = 'Processing', COMPLETED = 'Completed'
- Follow existing enum pattern in app/Enums/

## Task 2: Create PayrollPeriodType enum
- **Agent**: model
- **Files**: app/Enums/PayrollPeriodType.php
- Create PHP 8.4 string-backed enum with cases: FIRST_HALF = 'First Half', SECOND_HALF = 'Second Half'
- Used to distinguish semi-monthly cutoff periods and determine when government deductions apply

## Task 3: Create HolidayType enum
- **Agent**: model
- **Files**: app/Enums/HolidayType.php
- Create PHP 8.4 string-backed enum with cases: REGULAR = 'Regular Holiday', SPECIAL_NON_WORKING = 'Special Non-Working Day'
- Per Philippine holiday classification under the Labor Code

## Task 4: Create LoanType enum
- **Agent**: model
- **Files**: app/Enums/LoanType.php
- Create PHP 8.4 string-backed enum with cases: SSS_SALARY = 'SSS Salary Loan', SSS_CALAMITY = 'SSS Calamity Loan', PAGIBIG_MPL = 'Pag-IBIG Multi-Purpose Loan', PAGIBIG_CALAMITY = 'Pag-IBIG Calamity Loan', COMPANY = 'Company Loan'
- Covers all common government and company loan types deducted from Philippine payroll

## Task 5: Create payroll_periods migration
- **Agent**: migration
- **Files**: database/migrations/2026_04_01_100001_create_payroll_periods_table.php
- Create payroll_periods table with columns: id, name (string), periodType (string), startDate (date), endDate (date), payDate (date), status (string default 'Draft'), totalGrossPay (decimal:12,2 default 0), totalDeductions (decimal:12,2 default 0), totalNetPay (decimal:12,2 default 0), totalEmployerContributions (decimal:12,2 default 0), employeeCount (integer default 0), processedAt (datetime nullable), completedAt (datetime nullable), timestamps
- Call MigrationHelper::addSystemFields($table)
- Unique constraint on [startDate, endDate]

## Task 6: Create deduction_types migration
- **Agent**: migration
- **Files**: database/migrations/2026_04_01_100002_create_deduction_types_table.php
- Create deduction_types table with columns: id, name (string), code (string unique), description (text nullable), computationMethod (string), isStatutory (boolean default false), isActive (boolean default true), fixedAmount (decimal:10,2 nullable), employeeRate (decimal:5,4 nullable), employerRate (decimal:5,4 nullable), bracketTable (json nullable), salaryFloor (decimal:12,2 nullable), salaryCeiling (decimal:12,2 nullable), maxEmployeeAmount (decimal:10,2 nullable), maxEmployerAmount (decimal:10,2 nullable), timestamps
- Call MigrationHelper::addSystemFields($table)

## Task 7: Create holidays migration
- **Agent**: migration
- **Files**: database/migrations/2026_04_01_100003_create_holidays_table.php
- Create holidays table with columns: id, name (string), date (date), holidayType (string), year (integer), timestamps
- Call MigrationHelper::addSystemFields($table)
- Unique constraint on [date, name]

## Task 8: Create employee_loans migration
- **Agent**: migration
- **Files**: database/migrations/2026_04_01_100004_create_employee_loans_table.php
- Create employee_loans table with columns: id, employeeID (unsignedBigInteger FK to employees.employeeID cascadeOnDelete), loanType (string), referenceNbr (string nullable), loanAmount (decimal:12,2), monthlyAmortization (decimal:10,2), totalPaid (decimal:12,2 default 0), remainingBalance (decimal:12,2), startDate (date), endDate (date nullable), isActive (boolean default true), remarks (text nullable), timestamps
- Call MigrationHelper::addSystemFields($table)

## Task 9: Create payroll_records migration
- **Agent**: migration
- **Files**: database/migrations/2026_04_01_100005_create_payroll_records_table.php
- Create payroll_records table with columns:
  - id, payrollPeriodID (foreignId constrained to payroll_periods.id cascadeOnDelete), employeeID (unsignedBigInteger FK to employees.employeeID cascadeOnDelete)
  - Salary/rate: basicMonthlySalary (decimal:12,2), dailyRate (decimal:10,2), hourlyRate (decimal:10,2)
  - Attendance: daysWorked (decimal:5,2), daysAbsent (decimal:5,2), approvedLeaveDays (decimal:5,2 default 0), regularHoursWorked (decimal:7,2), overtimeHours (decimal:7,2), nightDifferentialHours (decimal:7,2 default 0), holidayDaysWorked (decimal:5,2 default 0), specialHolidayDaysWorked (decimal:5,2 default 0), lateMinutes (decimal:7,2 default 0), undertimeMinutes (decimal:7,2 default 0)
  - Earnings: basicPay (decimal:12,2), absentDeduction (decimal:12,2 default 0), lateDeduction (decimal:12,2 default 0), undertimeDeduction (decimal:12,2 default 0), overtimePay (decimal:12,2 default 0), nightDifferentialPay (decimal:12,2 default 0), holidayPay (decimal:12,2 default 0), specialHolidayPay (decimal:12,2 default 0), grossPay (decimal:12,2)
  - Mandatory deductions: sssEmployee (decimal:10,2 default 0), sssEmployer (decimal:10,2 default 0), sssEC (decimal:10,2 default 0), philhealthEmployee (decimal:10,2 default 0), philhealthEmployer (decimal:10,2 default 0), pagibigEmployee (decimal:10,2 default 0), pagibigEmployer (decimal:10,2 default 0), withholdingTax (decimal:10,2 default 0)
  - Totals: totalMandatoryDeductions (decimal:12,2 default 0), totalLoanDeductions (decimal:12,2 default 0), totalDeductions (decimal:12,2 default 0), netPay (decimal:12,2)
  - timestamps
- Call MigrationHelper::addSystemFields($table)
- Unique constraint on [payrollPeriodID, employeeID]

## Task 10: Create payroll_deductions migration
- **Agent**: migration
- **Files**: database/migrations/2026_04_01_100006_create_payroll_deductions_table.php
- Create payroll_deductions table with columns: id, payrollRecordID (foreignId constrained to payroll_records.id cascadeOnDelete), deductionTypeID (foreignId nullable constrained to deduction_types.id), employeeLoanID (foreignId nullable constrained to employee_loans.id), description (string), employeeAmount (decimal:10,2), employerAmount (decimal:10,2 default 0), remarks (text nullable), timestamps
- Call MigrationHelper::addSystemFields($table)

## Task 11: Create PayrollPeriod model
- **Agent**: model
- **Files**: app/Models/PayrollPeriod.php
- Use HasFactory, HasSystemFields traits
- fillable: name, periodType, startDate, endDate, payDate, status, totalGrossPay, totalDeductions, totalNetPay, totalEmployerContributions, employeeCount, processedAt, completedAt
- Casts: startDate date, endDate date, payDate date, periodType PayrollPeriodType, status PayrollPeriodStatus, totalGrossPay decimal:2, totalDeductions decimal:2, totalNetPay decimal:2, totalEmployerContributions decimal:2, processedAt datetime, completedAt datetime, CreatedDateTime datetime, LastModifiedDateTime datetime
- hasMany PayrollRecord (FK payrollPeriodID)

## Task 12: Create DeductionType model
- **Agent**: model
- **Files**: app/Models/DeductionType.php
- Use HasFactory, HasSystemFields traits
- fillable: name, code, description, computationMethod, isStatutory, isActive, fixedAmount, employeeRate, employerRate, bracketTable, salaryFloor, salaryCeiling, maxEmployeeAmount, maxEmployerAmount
- Casts: isStatutory boolean, isActive boolean, fixedAmount decimal:2, employeeRate decimal:4, employerRate decimal:4, bracketTable array (JSON), salaryFloor decimal:2, salaryCeiling decimal:2, maxEmployeeAmount decimal:2, maxEmployerAmount decimal:2, CreatedDateTime datetime, LastModifiedDateTime datetime
- hasMany PayrollDeduction (FK deductionTypeID)

## Task 13: Create Holiday model
- **Agent**: model
- **Files**: app/Models/Holiday.php
- Use HasFactory, HasSystemFields traits
- fillable: name, date, holidayType, year
- Casts: date date, holidayType HolidayType, CreatedDateTime datetime, LastModifiedDateTime datetime
- No relationships (standalone reference table)

## Task 14: Create EmployeeLoan model
- **Agent**: model
- **Files**: app/Models/EmployeeLoan.php
- Use HasFactory, HasSystemFields traits
- fillable: employeeID, loanType, referenceNbr, loanAmount, monthlyAmortization, totalPaid, remainingBalance, startDate, endDate, isActive, remarks
- Casts: loanType LoanType, loanAmount decimal:2, monthlyAmortization decimal:2, totalPaid decimal:2, remainingBalance decimal:2, startDate date, endDate date, isActive boolean, CreatedDateTime datetime, LastModifiedDateTime datetime
- belongsTo Employee (FK employeeID, owner key employeeID)
- hasMany PayrollDeduction (FK employeeLoanID)

## Task 15: Create PayrollRecord model
- **Agent**: model
- **Files**: app/Models/PayrollRecord.php
- Use HasFactory, HasSystemFields traits
- fillable: payrollPeriodID, employeeID, basicMonthlySalary, dailyRate, hourlyRate, daysWorked, daysAbsent, approvedLeaveDays, regularHoursWorked, overtimeHours, nightDifferentialHours, holidayDaysWorked, specialHolidayDaysWorked, lateMinutes, undertimeMinutes, basicPay, absentDeduction, lateDeduction, undertimeDeduction, overtimePay, nightDifferentialPay, holidayPay, specialHolidayPay, grossPay, sssEmployee, sssEmployer, sssEC, philhealthEmployee, philhealthEmployer, pagibigEmployee, pagibigEmployer, withholdingTax, totalMandatoryDeductions, totalLoanDeductions, totalDeductions, netPay
- Casts: all decimal fields → decimal:2, CreatedDateTime datetime, LastModifiedDateTime datetime
- belongsTo PayrollPeriod (FK payrollPeriodID), belongsTo Employee (FK employeeID, owner key employeeID)
- hasMany PayrollDeduction (FK payrollRecordID)

## Task 16: Create PayrollDeduction model
- **Agent**: model
- **Files**: app/Models/PayrollDeduction.php
- Use HasFactory, HasSystemFields traits
- fillable: payrollRecordID, deductionTypeID, employeeLoanID, description, employeeAmount, employerAmount, remarks
- Casts: employeeAmount decimal:2, employerAmount decimal:2, CreatedDateTime datetime, LastModifiedDateTime datetime
- belongsTo PayrollRecord (FK payrollRecordID), belongsTo DeductionType (FK deductionTypeID, nullable), belongsTo EmployeeLoan (FK employeeLoanID, nullable)

## Task 17: Add payroll and loan relationships to Employee model
- **Agent**: model
- **Files**: app/Models/Employee.php
- Add hasMany relationship: payrollRecords() → hasMany(PayrollRecord::class, 'employeeID', 'employeeID')
- Add hasMany relationship: employeeLoans() → hasMany(EmployeeLoan::class, 'employeeID', 'employeeID')

## Task 18: Create PayrollComputationService
- **Agent**: service
- **Files**: app/Services/PayrollComputationService.php
- Implement all methods described in the design:
  - computePayrollForEmployee(Employee, PayrollPeriod): Fetch attendance records for period, approved leaves, holidays. Compute daily/hourly rates from salary and work schedule. Calculate days worked/absent (excluding approved leave), hours/OT, night differential hours (overlap with 10 PM–6 AM), holiday days worked, late/undertime minutes. Compute basicPay (salary/2), absentDeduction, overtimePay (25% premium), nightDifferentialPay (10% premium), holidayPay (100% premium for regular, 30% for special), late/undertime deductions, grossPay. Return associative array.
  - computeMandatoryDeductions(PayrollRecord, PayrollPeriod): Only on Second Half periods. SSS bracket lookup on bracketTable, PhilHealth percentage with floor/ceiling, Pag-IBIG bracket with caps, withholding tax from BIR table on taxable income (gross - SSS EE - PhilHealth EE - PagIBIG EE). Return all amounts including employer shares.
  - computeLoanDeductions(Employee, PayrollPeriod): Get active loans, compute semi-monthly deduction (monthlyAmortization / 2), cap at remainingBalance. Return array of loan ID => amount.
  - computeWithholdingTax(float semiMonthlyTaxableIncome): BIR progressive bracket lookup. tax = base + rate × (income - bracket.min). Return max(0, tax).
  - calculateNightDifferentialHours(AttendanceRecord): Calculate overlap between actual work time and 10 PM–6 AM window.
  - calculateWorkingDaysInPeriod(Carbon start, Carbon end, string workingDays): Count schedule-matching days in range.

## Task 19: Create DeductionType factory and seeder
- **Agent**: model
- **Files**: database/factories/DeductionTypeFactory.php, database/seeders/DeductionTypeSeeder.php
- Factory: standard defaults (name, code, computationMethod = fixed, isStatutory = false)
- Seeder creates Philippine statutory deductions:
  - SSS (code: SSS, bracket method, isStatutory: true): bracketTable with full 2026 MSC table — MSC range ₱4,000–₱30,000, employeeShare/employerShare/ec per bracket, employeeRate 0.0450, employerRate 0.0950
  - PhilHealth (code: PHIC, percentage method, isStatutory: true): employeeRate 0.0250, employerRate 0.0250, salaryFloor 10000, salaryCeiling 100000
  - Pag-IBIG (code: HDMF, bracket method, isStatutory: true): bracketTable with 2 brackets (≤₱1,500 EE 1% ER 2%; >₱1,500 EE 2% ER 2%), maxEmployeeAmount 200, maxEmployerAmount 200
  - Withholding Tax (code: TAX, bracket method, isStatutory: true): bracketTable with 6 BIR semi-monthly brackets per TRAIN Law

## Task 20: Create Holiday factory and seeder
- **Agent**: model
- **Files**: database/factories/HolidayFactory.php, database/seeders/HolidaySeeder.php
- Factory: name, date, holidayType (random), year from date
- Seeder creates 2026 Philippine holidays:
  - Regular Holidays: New Year's Day (Jan 1), Araw ng Kagitingan (Apr 9), Maundy Thursday (Apr 2), Good Friday (Apr 3), Labor Day (May 1), Independence Day (Jun 12), National Heroes Day (Aug 31), Bonifacio Day (Nov 30), Christmas Day (Dec 25), Rizal Day (Dec 30)
  - Special Non-Working Days: Ninoy Aquino Day (Aug 21), All Saints' Day (Nov 1), Feast of the Immaculate Conception (Dec 8), Last Day of the Year (Dec 31), Chinese New Year (Feb 17), EDSA Anniversary (Feb 25), Black Saturday (Apr 4)

## Task 21: Create EmployeeLoan factory
- **Agent**: model
- **Files**: database/factories/EmployeeLoanFactory.php
- Employee factory, loanType (random LoanType), loanAmount between 5000–50000, monthlyAmortization computed from loanAmount, remainingBalance = loanAmount, startDate, isActive = true

## Task 22: Create PayrollPeriod, PayrollRecord, PayrollDeduction factories
- **Agent**: model
- **Files**: database/factories/PayrollPeriodFactory.php, database/factories/PayrollRecordFactory.php, database/factories/PayrollDeductionFactory.php
- PayrollPeriodFactory: name, periodType (random), date range, payDate, status Draft
- PayrollRecordFactory: PayrollPeriod + Employee factories, computed field defaults, all mandatory deduction fields default 0
- PayrollDeductionFactory: PayrollRecord factory, description, employeeAmount, employerAmount = 0

## Task 23: Update DatabaseSeeder
- **Agent**: model
- **Files**: database/seeders/DatabaseSeeder.php
- Add DeductionTypeSeeder and HolidaySeeder after existing seeders

## Task 24: Create PayrollPeriodController
- **Agent**: controller
- **Files**: app/Http/Controllers/PayrollPeriodController.php
- index: list periods with status filter tabs (All/Draft/Processing/Completed), year filter, pagination. Pass PayrollPeriodType and PayrollPeriodStatus cases to view.
- create: show form with periodType dropdown (PayrollPeriodType cases), date inputs
- store: validate (name, periodType, startDate, endDate, payDate) and create period in Draft status
- show: display period details with payroll records table (employees + pay breakdown). Show government contribution summary totals (SSS, PhilHealth, Pag-IBIG with employee + employer columns) for remittance. Pass records with employee eager-loaded.
- process: validate Draft status, use DB::transaction. Call PayrollComputationService for each active employee. Create PayrollRecord, then PayrollDeduction entries for mandatory deductions (2nd cutoff only) and loan deductions. Update EmployeeLoan balances. Sum period totals.
- complete: validate Processing status, set Completed + completedAt in DB::transaction
- destroy: only if Draft status, delete period

## Task 25: Create PayslipController
- **Agent**: controller
- **Files**: app/Http/Controllers/PayslipController.php
- index: get current user's employee, list their PayrollRecords from Completed periods with period info, ordered by payDate desc, paginate
- show: get PayrollRecord with deductions and period eager-loaded, verify belongs to current user's employee (abort 403 otherwise), return payslip view

## Task 26: Create EmployeeLoanController
- **Agent**: controller
- **Files**: app/Http/Controllers/EmployeeLoanController.php
- Full CRUD with inline validation. Pass LoanType cases and employees list to create/edit views.
- index: list loans with filters (employee search, loanType, isActive), pagination. Eager-load employee.user for display.
- store: validate (employeeID, loanType, referenceNbr, loanAmount, monthlyAmortization, startDate, endDate, remarks). Set remainingBalance = loanAmount, totalPaid = 0.
- update: validate same fields except employeeID and loanType (not editable after creation)
- show: loan details + deduction history from PayrollDeduction where employeeLoanID matches, with payroll period info
- destroy: prevent if PayrollDeduction records exist for this loan

## Task 27: Create HolidayController
- **Agent**: controller
- **Files**: app/Http/Controllers/HolidayController.php
- CRUD (no show page). Pass HolidayType cases to create/edit views.
- index: list holidays filtered by year (default current year), ordered by date. Year dropdown filter.
- store: validate (name, date, holidayType, year). Auto-derive year from date if not provided.
- update: same validation
- destroy: no restrictions

## Task 28: Create payroll routes
- **Agent**: routing
- **Files**: routes/web.php
- Add inside auth middleware group:
  - Route::post('payroll/{payrollPeriod}/process', [PayrollPeriodController, 'process'])->name('payroll.process')
  - Route::post('payroll/{payrollPeriod}/complete', [PayrollPeriodController, 'complete'])->name('payroll.complete')
  - Route::resource('payroll', PayrollPeriodController)->except(['edit', 'update'])
  - Route::get('payslips', [PayslipController, 'index'])->name('payslips.index')
  - Route::get('payslips/{payrollRecord}', [PayslipController, 'show'])->name('payslips.show')
  - Route::resource('employee-loans', EmployeeLoanController)
  - Route::resource('holidays', HolidayController)->except(['show'])

## Task 29: Create payroll period Blade views
- **Agent**: ui-ux
- **Files**: resources/views/payroll/index.blade.php, create.blade.php, show.blade.php
- index: status filter tabs (All/Draft/Processing/Completed), data table with Period Name, Type badge, Date Range, Pay Date, Status badge, Employee Count, Total Net Pay (₱ formatted), Actions (View, Delete for Draft)
- create: form with name, periodType dropdown (First Half / Second Half), startDate, endDate, payDate
- show: period summary card (status badge, dates, totals for gross/deductions/net/employer). Payroll records data table: Employee Name, Basic Pay, OT Pay, ND Pay, Holiday Pay, Gross Pay, Mandatory Deductions, Loan Deductions, Net Pay. Process button (if Draft), Complete button (if Processing). Government contribution summary section: totals for SSS (EE + ER + EC), PhilHealth (EE + ER), Pag-IBIG (EE + ER) in a summary card.

## Task 30: Create payslip Blade views
- **Agent**: ui-ux
- **Files**: resources/views/payslips/index.blade.php, show.blade.php
- index: list of completed payslip periods — Period Name, Type, Pay Date, Net Pay (₱), View link
- show: structured payslip layout —
  - Company header + employee info (name, department, position, employee ID)
  - Period info (name, type, date range)
  - Earnings table: Basic Pay, less Absent Deduction, less Late Deduction, less Undertime Deduction, Overtime Pay, Night Differential Pay, Holiday Pay, Special Holiday Pay = Gross Pay
  - Attendance summary: Days Worked, Days Absent, Approved Leave Days, OT Hours, ND Hours, Late Min, Undertime Min
  - Mandatory deductions table (only if 2nd cutoff: period.periodType == Second Half): SSS, PhilHealth, Pag-IBIG, Withholding Tax
  - Loan deductions table: each loan description + amount (from PayrollDeductions where employeeLoanID is not null)
  - Summary footer: Gross Pay, Total Mandatory Deductions, Total Loan Deductions, Total Deductions, **Net Pay**

## Task 31: Create employee loan Blade views
- **Agent**: ui-ux
- **Files**: resources/views/employee-loans/index.blade.php, create.blade.php, edit.blade.php, show.blade.php
- index: data table with Employee Name, Loan Type badge, Ref #, Loan Amount (₱), Monthly Amortization (₱), Remaining Balance (₱), Active/Inactive badge, Actions. Filters: employee search input, loanType dropdown, active status dropdown.
- create: form with employee dropdown (searchable), loanType dropdown, referenceNbr, loanAmount, monthlyAmortization, startDate, endDate, remarks textarea
- edit: same form pre-populated, employeeID and loanType fields disabled/readonly
- show: loan summary card (all fields displayed) + deduction history data table: Payroll Period, Date, Amount Deducted (₱), Balance After

## Task 32: Create holiday Blade views
- **Agent**: ui-ux
- **Files**: resources/views/holidays/index.blade.php, create.blade.php, edit.blade.php
- index: year filter dropdown (default current year), data table with Name, Date (formatted), Type badge (Regular Holiday in red/danger, Special Non-Working Day in yellow/warning), Actions (Edit, Delete). Add Holiday button.
- create: form with name, date input, holidayType dropdown, year (auto-filled from date via Alpine.js)
- edit: same form pre-populated

## Task 33: Update sidebar navigation for payroll section
- **Agent**: ui-ux
- **Files**: resources/views/layouts/sidebar.blade.php
- Update existing "Payroll" section links: Payroll Periods (/payroll) with payroll.* route matching, My Payslips (/payslips) with payslips.* matching, Employee Loans (/employee-loans) with employee-loans.* matching, Holidays (/holidays) with holidays.* matching
- Follow existing sidebar link pattern with SVG icons and expanded/collapsed states

## Task 34: Create Pest tests for PayrollPeriod
- **Agent**: testing
- **Files**: tests/Feature/PayrollPeriodTest.php
- Test CRUD: can create period with valid data, validation errors for missing fields, validates periodType is valid enum
- Test status transitions: Draft→Processing via process route, Processing→Completed via complete route
- Test guards: cannot process non-Draft period (redirect with error), cannot complete non-Processing period, cannot delete non-Draft period
- Test process: generates PayrollRecord for each active employee, skips inactive employees, period totals are correctly summed
- Test uniqueness: cannot create duplicate period with same startDate+endDate

## Task 35: Create Pest tests for PayrollComputationService
- **Agent**: testing
- **Files**: tests/Feature/PayrollComputationServiceTest.php
- Test daily rate: basicMonthlySalary / working days per month (22 for 5-day, 26 for 6-day)
- Test hourly rate: dailyRate / totalWorkHours
- Test basic pay: basicMonthlySalary / 2 for semi-monthly
- Test absent deduction: dailyRate × daysAbsent; approved leave not counted as absent
- Test OT pay: hourlyRate × overtimeHours × 0.25
- Test night differential: hourlyRate × ndHours × 0.10
- Test holiday pay: regular holiday 100% premium, special holiday 30% premium
- Test late deduction: hourlyRate × (lateMinutes / 60)
- Test undertime deduction: hourlyRate × (undertimeMinutes / 60)
- Test gross pay formula: basicPay - absentDeduction - lateDeduction - undertimeDeduction + overtimePay + ndPay + holidayPay + specialHolidayPay
- Test SSS: bracket lookup returns correct employeeShare, employerShare, ec for various salary levels
- Test PhilHealth: 2.5% employee, 2.5% employer; clamped at floor ₱10,000 and ceiling ₱100,000
- Test Pag-IBIG: 1% for salary ≤₱1,500, 2% for >₱1,500, capped at ₱200
- Test withholding tax: correct computation across all 6 BIR brackets; tax on income after mandatory deductions
- Test mandatory deductions only on Second Half: First Half returns all zeros
- Test loan deduction: monthlyAmortization / 2; capped at remainingBalance
- Test loan balance update: remainingBalance decremented, totalPaid incremented

## Task 36: Create Pest tests for Payslips
- **Agent**: testing
- **Files**: tests/Feature/PayslipTest.php
- Test index: employee sees own payslips from completed periods only
- Test show: employee sees full pay breakdown including earnings, deductions, net
- Test authorization: employee cannot view another employee's payslip (403)
- Test First Half payslip: mandatory deductions section shows zeros
- Test Second Half payslip: mandatory deductions section shows SSS, PhilHealth, Pag-IBIG, Tax amounts

## Task 37: Create Pest tests for EmployeeLoan
- **Agent**: testing
- **Files**: tests/Feature/EmployeeLoanTest.php
- Test CRUD: create, read, update, delete loan records
- Test validation: required fields, valid loanType, positive amounts
- Test balance: totalPaid + remainingBalance = loanAmount invariant after deductions
- Test delete guard: cannot delete loan with existing PayrollDeduction records
- Test show: displays deduction history from linked PayrollDeductions with period info

## Task 38: Create Pest tests for Holiday
- **Agent**: testing
- **Files**: tests/Feature/HolidayTest.php
- Test CRUD: create, edit, update, delete holidays
- Test validation: required fields, valid holidayType
- Test year filter: index shows only holidays for selected year
- Test uniqueness: cannot create duplicate holiday with same date+name
