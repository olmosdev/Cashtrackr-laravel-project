<?php

use App\Models\Budget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows the owner to update a budget', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $budget = Budget::factory()->for($user)->create([
        'name' => 'Old Budget Name',
        'amount' => 1000,
        'type' => 'general',
    ]);

    $response = $this->actingAs($user)
        ->put(route('budgets.update', $budget), [
            'name' => 'Updated Budget Name',
            'amount' => 2000,
            'type' => 'goal',
        ]);

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success', 'Presupuesto actualizado exitosamente');

    $this->assertDatabaseHas('budgets', [
        'id' => $budget->id,
        'name' => 'Updated Budget Name',
        'amount' => 2000,
        'type' => 'goal',
        'user_id' => $user->id,
    ]); 

});

it('validates required fields when updating a budget', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $budget = Budget::factory()->for($user)->create();

    $response = $this->actingAs($user)
        ->from(route('budgets.edit', $budget))
        ->put(route('budgets.update', $budget), [
            'name' => '',
            'amount' => '',
            'type' => '',
        ]);

    $response->assertRedirect(route('budgets.edit', $budget));
    $response->assertSessionHasErrors(['name', 'amount', 'type']);
});

it('validates amount must be greater than zero when updating a budget', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $budget = Budget::factory()->for($user)->create();

    $response = $this->actingAs($user)
        ->from(route('budgets.edit', $budget))
        ->put(route('budgets.update', $budget), [
            'name' => 'Budget',
            'amount' => 0,
            'type' => 'general',
        ]);

    $response->assertRedirect(route('budgets.edit', $budget));
    $response->assertSessionHasErrors(['amount']);


});

it('validates type must be valid when updating a budget', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $budget = Budget::factory()->for($user)->create();

    $response = $this->actingAs($user)
        ->from(route('budgets.edit', $budget))
        ->put(route('budgets.update', $budget), [
            'name' => 'Budget',
            'amount' => 1000,
            'type' => 'not-a-valid-type',
        ]);

    $response->assertRedirect(route('budgets.edit', $budget));
    $response->assertSessionHasErrors(['type']);

});

it('does not allow guests to update budgets', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $budget = Budget::factory()->for($user)->create();

    $response = $this->put(route('budgets.update', $budget), [
        'name' => 'Updated Budget Name',
        'amount' => 2000,
        'type' => 'goal',
    ]);

    $response->assertRedirect(route('login'));

});

it('does not allow other users to update budgets', function () {
    $owner = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $otherUser = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $budget = Budget::factory()->for($owner)->create([
        'name' => 'Original Budget Name'
    ]);

    $response = $this->actingAs($otherUser)
        ->put(route('budgets.update', $budget), [
            'name' => 'Hacked Budget Name',
            'amount' => 2000,
            'type' => 'goal',
        ]);
    
    $response->assertForbidden();

    $this->assertDatabaseHas('budgets', [
        'id' => $budget->id,
        'name' => 'Original Budget Name'
    ]);
});




