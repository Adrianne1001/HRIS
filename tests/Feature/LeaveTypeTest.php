<?php

use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\User;

test('leave types index page is displayed', function () {
    $user = User::factory()->create();
    LeaveType::factory()->create(['name' => 'Vacation Leave', 'code' => 'VL']);

    $response = $this->actingAs($user)->get('/leave-types');

    $response->assertOk();
    $response->assertSee('Vacation Leave');
});

test('leave type can be created', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/leave-types', [
        'name' => 'Vacation Leave',
        'code' => 'VL',
        'defaultCredits' => 15,
        'description' => 'Annual vacation leave',
        'isActive' => true,
        'isPaid' => true,
        'requiresDocument' => false,
    ]);

    $response->assertRedirect('/leave-types');
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('leave_types', ['code' => 'VL', 'name' => 'Vacation Leave']);
});

test('leave type requires name and code', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/leave-types', [
        'defaultCredits' => 15,
    ]);

    $response->assertSessionHasErrors(['name', 'code']);
});

test('leave type code must be unique', function () {
    $user = User::factory()->create();
    LeaveType::factory()->create(['code' => 'VL']);

    $response = $this->actingAs($user)->post('/leave-types', [
        'name' => 'Another Leave',
        'code' => 'VL',
        'defaultCredits' => 10,
    ]);

    $response->assertSessionHasErrors(['code']);
});

test('leave type can be updated', function () {
    $user = User::factory()->create();
    $leaveType = LeaveType::factory()->create(['name' => 'Old Name', 'code' => 'OL']);

    $response = $this->actingAs($user)->put("/leave-types/{$leaveType->id}", [
        'name' => 'New Name',
        'code' => 'NL',
        'defaultCredits' => 20,
        'isActive' => true,
        'isPaid' => true,
        'requiresDocument' => false,
    ]);

    $response->assertRedirect('/leave-types');
    $this->assertDatabaseHas('leave_types', ['id' => $leaveType->id, 'name' => 'New Name', 'code' => 'NL']);
});

test('leave type can be deleted when no requests exist', function () {
    $user = User::factory()->create();
    $leaveType = LeaveType::factory()->create();

    $response = $this->actingAs($user)->delete("/leave-types/{$leaveType->id}");

    $response->assertRedirect('/leave-types');
    $this->assertDatabaseMissing('leave_types', ['id' => $leaveType->id]);
});

test('leave type cannot be deleted when requests exist', function () {
    $user = User::factory()->create();
    $leaveType = LeaveType::factory()->create();
    LeaveRequest::factory()->create(['leaveTypeID' => $leaveType->id]);

    $response = $this->actingAs($user)->delete("/leave-types/{$leaveType->id}");

    $response->assertRedirect('/leave-types');
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('leave_types', ['id' => $leaveType->id]);
});

test('leave type show page is displayed', function () {
    $user = User::factory()->create();
    $leaveType = LeaveType::factory()->create(['name' => 'Sick Leave', 'code' => 'SL']);

    $response = $this->actingAs($user)->get("/leave-types/{$leaveType->id}");

    $response->assertOk();
    $response->assertSee('Sick Leave');
});
