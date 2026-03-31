<?php

use App\Enums\HolidayType;
use App\Models\Holiday;
use App\Models\User;

it('can view holidays index', function () {
    $user = User::factory()->create();
    Holiday::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('holidays.index'));

    $response->assertOk();
});

it('index filters holidays by year', function () {
    $user = User::factory()->create();

    $holiday2025 = Holiday::factory()->create([
        'name' => 'Old Holiday',
        'date' => '2025-06-12',
        'year' => 2025,
    ]);

    $holiday2026 = Holiday::factory()->create([
        'name' => 'Current Year Holiday',
        'date' => '2026-06-12',
        'year' => 2026,
    ]);

    $response = $this->actingAs($user)->get(route('holidays.index', ['year' => 2026]));

    $response->assertOk();
    $response->assertViewHas('holidays', function ($holidays) use ($holiday2026, $holiday2025) {
        $ids = $holidays->pluck('id');
        return $ids->contains($holiday2026->id) && ! $ids->contains($holiday2025->id);
    });
});

it('can create a holiday with valid data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('holidays.store'), [
        'name' => 'Independence Day',
        'date' => '2026-06-12',
        'holidayType' => HolidayType::REGULAR->value,
        'year' => 2026,
    ]);

    $response->assertRedirect(route('holidays.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('holidays', [
        'name' => 'Independence Day',
        'holidayType' => HolidayType::REGULAR->value,
        'year' => 2026,
    ]);
});

it('store validation fails for missing required fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('holidays.store'), []);

    $response->assertSessionHasErrors(['name', 'date', 'holidayType', 'year']);
});

it('store validates holidayType is a valid enum value', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('holidays.store'), [
        'name' => 'Test Holiday',
        'date' => '2026-06-12',
        'holidayType' => 'Invalid Type',
        'year' => 2026,
    ]);

    $response->assertSessionHasErrors(['holidayType']);
});

it('can update a holiday', function () {
    $user = User::factory()->create();
    $holiday = Holiday::factory()->create([
        'holidayType' => HolidayType::REGULAR,
    ]);

    $response = $this->actingAs($user)->put(route('holidays.update', $holiday), [
        'name' => 'Updated Holiday Name',
        'date' => $holiday->date->format('Y-m-d'),
        'holidayType' => HolidayType::SPECIAL_NON_WORKING->value,
        'year' => $holiday->year,
    ]);

    $response->assertRedirect(route('holidays.index'));
    $response->assertSessionHas('success');
    expect($holiday->fresh()->name)->toBe('Updated Holiday Name');
    expect($holiday->fresh()->holidayType)->toBe(HolidayType::SPECIAL_NON_WORKING);
});

it('can delete a holiday', function () {
    $user = User::factory()->create();
    $holiday = Holiday::factory()->create();

    $response = $this->actingAs($user)->delete(route('holidays.destroy', $holiday));

    $response->assertRedirect(route('holidays.index'));
    $this->assertDatabaseMissing('holidays', ['id' => $holiday->id]);
});

it('cannot create a duplicate holiday with same date and name', function () {
    $user = User::factory()->create();

    Holiday::factory()->create([
        'name' => 'Independence Day',
        'date' => '2026-06-12',
        'year' => 2026,
    ]);

    $this->actingAs($user)->post(route('holidays.store'), [
        'name' => 'Independence Day',
        'date' => '2026-06-12',
        'holidayType' => HolidayType::REGULAR->value,
        'year' => 2026,
    ]);

    $this->assertDatabaseCount('holidays', 1);
});

it('unauthenticated user is redirected to login for holidays', function () {
    $response = $this->get(route('holidays.index'));

    $response->assertRedirect(route('login'));
});
