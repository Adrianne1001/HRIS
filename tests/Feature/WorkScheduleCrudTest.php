<?php

use App\Models\User;
use App\Models\WorkSchedule;

function expectTimeOnlyColumns(WorkSchedule $workSchedule): void
{
    foreach (['startTime', 'endTime', 'startBreakTime', 'endBreakTime'] as $column) {
        $value = $workSchedule->getRawOriginal($column);

        if ($value === null) {
            continue;
        }

        expect($value)->toMatch('/^\d{2}:\d{2}(:\d{2})?$/');
    }
}

test('authenticated users can create work schedules with time-only values', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('work-schedules.store'), [
        'name' => 'Regular Day Shift',
        'startTime' => '08:00',
        'endTime' => '17:00',
        'startBreakTime' => '12:00',
        'endBreakTime' => '13:00',
        'workingDays' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
        'isDefault' => '1',
    ]);

    $response->assertRedirect(route('work-schedules.index'));

    $workSchedule = WorkSchedule::query()->sole();

    expect($workSchedule->workingDays)->toBe('Mon,Tue,Wed,Thu,Fri');
    expect($workSchedule->totalWorkHours)->toBe('8.00');
    expect($workSchedule->isDefault)->toBeTrue();

    expectTimeOnlyColumns($workSchedule);
});

test('authenticated users can update work schedules with time-only values', function () {
    $user = User::factory()->create();
    $workSchedule = WorkSchedule::create([
        'name' => 'Regular Day Shift',
        'startTime' => '08:00',
        'endTime' => '17:00',
        'startBreakTime' => '12:00',
        'endBreakTime' => '13:00',
        'workingDays' => 'Mon,Tue,Wed,Thu,Fri',
        'totalWorkHours' => 8,
        'isDefault' => false,
    ]);

    $response = $this->actingAs($user)->put(route('work-schedules.update', $workSchedule), [
        'name' => 'Night Shift',
        'startTime' => '22:00',
        'endTime' => '06:00',
        'startBreakTime' => '02:00',
        'endBreakTime' => '03:00',
        'workingDays' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        'isDefault' => '1',
    ]);

    $response->assertRedirect(route('work-schedules.index'));

    $workSchedule->refresh();

    expect($workSchedule->name)->toBe('Night Shift');
    expect($workSchedule->workingDays)->toBe('Mon,Tue,Wed,Thu,Fri,Sat');
    expect($workSchedule->totalWorkHours)->toBe('7.00');
    expect($workSchedule->isDefault)->toBeTrue();

    expectTimeOnlyColumns($workSchedule);
});