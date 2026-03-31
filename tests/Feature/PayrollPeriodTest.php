<?php

use App\Enums\EmploymentStatus;
use App\Enums\PayrollPeriodStatus;
use App\Enums\PayrollPeriodType;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\User;
use App\Models\WorkSchedule;
use Database\Seeders\DeductionTypeSeeder;

it('can view payroll periods index', function () {
    $user = User::factory()->create();
    PayrollPeriod::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('payroll.index'));

    $response->assertOk();
});

it('can create payroll period with valid data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('payroll.store'), [
        'name' => 'April 1-15, 2026',
        'periodType' => PayrollPeriodType::FIRST_HALF->value,
        'startDate' => '2026-04-01',
        'endDate' => '2026-04-15',
        'payDate' => '2026-04-20',
    ]);

    $response->assertRedirect(route('payroll.index'));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('payroll_periods', [
        'name' => 'April 1-15, 2026',
        'status' => PayrollPeriodStatus::DRAFT->value,
    ]);
});

it('store validation fails for missing required fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('payroll.store'), []);

    $response->assertSessionHasErrors(['name', 'periodType', 'startDate', 'endDate', 'payDate']);
});

it('store validates periodType is a valid enum value', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('payroll.store'), [
        'name' => 'Test Period',
        'periodType' => 'Invalid Quarter',
        'startDate' => '2026-04-01',
        'endDate' => '2026-04-15',
        'payDate' => '2026-04-20',
    ]);

    $response->assertSessionHasErrors(['periodType']);
});

it('can view payroll period show page', function () {
    $user = User::factory()->create();
    $period = PayrollPeriod::factory()->create();

    $response = $this->actingAs($user)->get(route('payroll.show', $period));

    $response->assertOk();
});

it('cannot create duplicate period with same startDate and endDate', function () {
    $user = User::factory()->create();

    PayrollPeriod::factory()->create([
        'startDate' => '2026-04-01',
        'endDate' => '2026-04-15',
    ]);

    $this->actingAs($user)->post(route('payroll.store'), [
        'name' => 'Duplicate Period',
        'periodType' => PayrollPeriodType::FIRST_HALF->value,
        'startDate' => '2026-04-01',
        'endDate' => '2026-04-15',
        'payDate' => '2026-04-20',
    ]);

    $this->assertDatabaseCount('payroll_periods', 1);
});

it('can process a Draft period and creates PayrollRecords for active employees', function () {
    $this->seed(DeductionTypeSeeder::class);

    $user = User::factory()->create();

    $schedule = WorkSchedule::factory()->create([
        'workingDays' => 'Mon,Tue,Wed,Thu,Fri',
        'totalWorkHours' => 8,
    ]);

    Employee::factory()->count(2)->create([
        'employmentStatus' => EmploymentStatus::ACTIVE,
        'workScheduleID' => $schedule->id,
        'basicMonthlySalary' => 25000,
    ]);

    $period = PayrollPeriod::factory()->create([
        'status' => PayrollPeriodStatus::DRAFT,
        'periodType' => PayrollPeriodType::SECOND_HALF,
        'startDate' => '2026-04-16',
        'endDate' => '2026-04-30',
    ]);

    $response = $this->actingAs($user)->post(route('payroll.process', $period));

    $response->assertRedirect(route('payroll.show', $period));

    $period->refresh();
    expect($period->status)->toBe(PayrollPeriodStatus::PROCESSING);
    expect($period->employeeCount)->toBe(2);
    $this->assertDatabaseCount('payroll_records', 2);
});

it('cannot process a non-Draft period', function () {
    $user = User::factory()->create();

    $period = PayrollPeriod::factory()->create([
        'status' => PayrollPeriodStatus::PROCESSING,
    ]);

    $response = $this->actingAs($user)->post(route('payroll.process', $period));

    $response->assertRedirect(route('payroll.show', $period));
    $response->assertSessionHas('error');
});

it('can complete a Processing period', function () {
    $user = User::factory()->create();

    $period = PayrollPeriod::factory()->create([
        'status' => PayrollPeriodStatus::PROCESSING,
    ]);

    $response = $this->actingAs($user)->post(route('payroll.complete', $period));

    $response->assertRedirect(route('payroll.show', $period));
    $period->refresh();
    expect($period->status)->toBe(PayrollPeriodStatus::COMPLETED);
});

it('cannot complete a non-Processing period', function () {
    $user = User::factory()->create();

    $period = PayrollPeriod::factory()->create([
        'status' => PayrollPeriodStatus::DRAFT,
    ]);

    $response = $this->actingAs($user)->post(route('payroll.complete', $period));

    $response->assertRedirect(route('payroll.show', $period));
    $response->assertSessionHas('error');
});

it('can delete a Draft period', function () {
    $user = User::factory()->create();

    $period = PayrollPeriod::factory()->create([
        'status' => PayrollPeriodStatus::DRAFT,
    ]);

    $response = $this->actingAs($user)->delete(route('payroll.destroy', $period));

    $response->assertRedirect(route('payroll.index'));
    $this->assertDatabaseMissing('payroll_periods', ['id' => $period->id]);
});

it('cannot delete a non-Draft period', function () {
    $user = User::factory()->create();

    $period = PayrollPeriod::factory()->create([
        'status' => PayrollPeriodStatus::PROCESSING,
    ]);

    $response = $this->actingAs($user)->delete(route('payroll.destroy', $period));

    $response->assertRedirect(route('payroll.index'));
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('payroll_periods', ['id' => $period->id]);
});

it('unauthenticated user is redirected to login for payroll pages', function () {
    $response = $this->get(route('payroll.index'));

    $response->assertRedirect(route('login'));
});
