# Proposal: Payroll & Compensation

## Problem

FortiTech HRIS tracks `basicMonthlySalary` per employee and calculates `hoursWorked`, `advanceOTHours`, and `afterShiftOTHours` on each AttendanceRecord, but there is no payroll system connecting these data points. Currently:

- No concept of payroll periods (semi-monthly cutoffs aligned with Philippine standard)
- No way to compute gross pay from attendance data
- No mandatory government deduction processing — SSS, PhilHealth, Pag-IBIG contributions are legally required for all covered employees under Philippine law
- No withholding tax computation using BIR tax tables (TRAIN Law)
- No tracking of employer contribution shares required for government remittances (SSS employer + EC, PhilHealth employer, Pag-IBIG employer)
- No overtime pay computation per Philippine Labor Code (Art. 87): 125% for regular OT, with higher multipliers for rest days and holidays
- No night differential tracking (Art. 86): 10% premium for work between 10:00 PM and 6:00 AM — critical for a company with shift workers
- No holiday pay computation — Regular Holidays (200% if worked) and Special Non-Working Days (130% if worked) per the Labor Code
- No mechanism to deduct outstanding loan amortizations (SSS Salary Loan, Pag-IBIG Multi-Purpose Loan, company cash advances) from payroll
- No payslip generation for employees to view their pay breakdown
- No payroll summary for HR/finance to review before disbursement
- Manual payroll computation is error-prone and time-consuming

## Solution

Build a Payroll & Compensation module fully aligned with Philippine payroll regulations:

1. **Payroll Periods** — Semi-monthly cutoffs (1st–15th and 16th–end of month), the Philippine standard. Each period tracks its type (First Half / Second Half), date range, status, and pay date.

2. **Payroll Records** — Per-employee computation for each period. Aggregates attendance data into earnings (basic pay, overtime, night differential, holiday premiums). Applies all mandatory deductions and computes net pay.

3. **Mandatory Government Contributions** — Automatic computation of employee and employer shares:
   - **SSS**: Based on Monthly Salary Credit (MSC) bracket table — employee share (4.5%), employer share (9.5%), plus Employees' Compensation (EC)
   - **PhilHealth**: 5% of basic monthly salary split 50/50 between employee and employer (floor ₱10,000, ceiling ₱100,000)
   - **Pag-IBIG**: Employee 1–2% of salary (max ₱200/month), employer 2% (max ₱200/month)
   - Government contributions are deducted on the 2nd cutoff (16th–end) per common Philippine practice

4. **Withholding Tax** — BIR progressive tax table (TRAIN Law) applied to semi-monthly taxable income. Taxable income = gross pay minus SSS, PhilHealth, and Pag-IBIG employee contributions (these are tax-deductible).

5. **Employee Loans** — Track outstanding loans (SSS Salary Loan, SSS Calamity Loan, Pag-IBIG Multi-Purpose Loan, Pag-IBIG Calamity Loan, Company Loan) with remaining balance and monthly amortization. Loan deductions are automatically applied during payroll processing and balances are updated.

6. **Holiday Calendar** — Maintain Philippine holidays (Regular Holidays and Special Non-Working Days) per year. Used to compute holiday pay premiums for employees who work on these days.

7. **Night Differential** — Track hours worked between 10:00 PM and 6:00 AM per attendance record. Apply 10% premium on hourly rate per Art. 86 of the Labor Code.

8. **Overtime Computation** — Regular OT at 125% of hourly rate per Art. 87. Rest day and holiday OT at correspondingly higher rates per the Labor Code.

9. **Payslip Generation** — Employee self-service view of pay breakdown: earnings (basic, OT, night differential, holiday pay), mandatory deductions (SSS, PhilHealth, Pag-IBIG, tax), loan deductions, and net pay.

10. **Payroll Summary** — Admin view to review all employee payroll records for a period, total government contributions for remittance, and total loan deductions before marking as completed.

## Scope

### In Scope
- Payroll period management (create, process, complete) with semi-monthly periods
- Payroll record generation from attendance data
- Basic pay computation: (basicMonthlySalary / 2) for semi-monthly, adjusted for absences
- Overtime pay computation: hourly rate × OT hours × OT multiplier (125% regular, 130% rest day, 200%+ holiday)
- Night differential computation: hourly rate × ND hours × 10%
- Holiday pay computation: premium pay for employees who work on Regular Holidays (200%) and Special Non-Working Days (130%)
- SSS contribution lookup — 2026 MSC bracket table, both employee (4.5%) and employer (9.5%) shares, plus EC
- PhilHealth contribution — 5% of basic salary, 50/50 split, with floor/ceiling
- Pag-IBIG contribution — bracket-based, both employee (1–2%, max ₱200) and employer (2%, max ₱200) shares
- Withholding tax — BIR semi-monthly tax table (TRAIN Law), applied after deducting mandatory contributions from gross
- Employee loan tracking — SSS, Pag-IBIG, and company loans with balance and amortization management
- Automatic loan amortization deduction during payroll processing with balance updates
- Philippine holiday calendar management (Regular Holidays, Special Non-Working Days)
- Employer contribution tracking for government remittance compliance
- Late and undertime deduction computation
- Absent (AWOL) deduction computation
- Approved leave integration — approved leave days are not counted as absent
- Payslip view for employees
- Payroll summary/review for admin with contribution remittance totals
- Database seeders for government contribution tables (SSS, PhilHealth, Pag-IBIG, BIR tax)
- Database seeder for default Philippine holidays

### Non-Goals
- Role-based access control (future change — all authenticated users can access for now)
- PDF payslip export/download
- Bank file generation for direct deposit
- 13th Month Pay computation (mandatory but computed annually — planned as a separate change)
- De minimis benefits tracking
- Final pay / separation pay computation
- Government remittance report generation (SBR, RF-1, etc.)
- Payroll approval workflow (future — payroll is admin-processed for now)
- Integration with accounting systems
- Rest day + holiday compound OT multipliers (future refinement)
- Multiple pay frequency support (only semi-monthly for now)

## Key Decisions

1. **Semi-monthly default** — Payroll periods default to semi-monthly (1st–15th and 16th–end of month), the most common Philippine payroll cycle. Period type (First Half / Second Half) determines when government deductions apply.

2. **Government deductions on 2nd cutoff** — SSS, PhilHealth, Pag-IBIG, and withholding tax are deducted on the Second Half period only, following common Philippine payroll practice. This keeps the First Half payslip simpler and concentrates deductions.

3. **Attendance-driven** — Gross pay is computed from actual attendance records in the period, not assumed full attendance.

4. **Semi-monthly basic pay** — Base pay per period = basicMonthlySalary / 2. Absent days are deducted: dailyRate × daysAbsent. Approved leaves are not counted as absent.

5. **Daily rate derivation** — dailyRate = basicMonthlySalary / standard working days per month (use work schedule's working days to determine, default 26 for 6-day weeks or 22 for 5-day weeks).

6. **Hourly rate** — hourlyRate = dailyRate / totalWorkHours from the employee's work schedule.

7. **OT multipliers** — Regular day OT: 125%. Future phases will add rest day (130%), regular holiday (200% base + 130% OT), and special holiday (130% base + 130% OT) compound rates.

8. **Night differential** — 10% of hourly rate for hours between 10 PM and 6 AM, computed from attendance time records.

9. **Holiday pay** — Regular Holiday worked: 200% of daily rate (100% premium since monthly-paid employees already receive base pay). Special Non-Working Day worked: 130% of daily rate (30% premium). Unworked holidays for monthly-paid employees: already covered by monthly salary.

10. **Tax computation flow** — Taxable income = semi-monthly gross pay − (SSS employee share + PhilHealth employee share + Pag-IBIG employee share). Withholding tax is computed from this using the BIR semi-monthly tax table.

11. **Loan deductions** — Active loans are deducted during payroll processing. Monthly amortization is split across both cutoffs (÷2) unless the remaining balance is less than the amortization. Remaining balance is updated after each deduction.

12. **Payroll period lifecycle** — Draft → Processing → Completed. Records are generated when processing starts. Admin reviews, then marks as Completed. No edits after completion.

13. **One payroll record per employee per period** — No duplicates. Unique constraint on [payrollPeriodID, employeeID].

14. **Employer contributions tracked, not deducted** — Employer shares of SSS, PhilHealth, Pag-IBIG, and SSS EC are recorded on each PayrollDeduction for remittance tracking but are not deducted from the employee's net pay.
