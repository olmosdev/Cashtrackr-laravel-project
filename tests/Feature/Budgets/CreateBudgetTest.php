<?php

use App\Models\Budget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it("validates required fields when creating a budget", function() {
    $user = User::factory()->create([
        "email_verified_at" => now(),
    ]);

    $response = $this->actingAs($user)
        ->from(route("budgets.create"))
        ->post(route("budgets.store"), [
            "name" => "",
            "amount" => "",
            "type" => "",
        ]);

    $response->assertRedirect(route("budgets.create"));

    $response->assertSessionHasErrors([
            "name",
            "amount",
            "type",
    ]);
});

it("does not allow guest to create a budget", function() {
    $response = $this->post(route("budgets.store"), [
        "name" => "Wedding",
        "amount" => 1000,
        "type" => "goal",
    ]);

    $response->assertRedirect(route("login"));
});

it("assigns the created budget to the authenticated user", function() {
    $user = User::factory()->create([
        "email_verified_at" => now(),
    ]);

    $this->actingAs($user)
        ->post(route("budgets.store"), [
            "name" => "Travel to Asia",
            "amount" => 1000,
            "type" => "goal",
        ]);

    $this->assertDatabaseHas("budgets", [
        "name" => "Travel to Asia",
        "amount" => 1000,
        "type" => "goal",
        "user_id" => $user->id,
    ]);

    $budget = Budget::first();
    expect($budget->user_id)->toBe($user->id);
});

it("creates a budget and redirects with a success message", function() {
    $user = User::factory()->create([
        "email_verified_at" => now(),
    ]);

    $response = $this->actingAs($user)
        ->post(route("budgets.store"), [
            "name" => "Travel to Asia",
            "amount" => 1000,
            "type" => "goal",
        ]);

    $response->assertRedirect(route("dashboard"));
    $response->assertSessionHas("success", "Presupuesto creado exitosamente");
});

it("does not allow unverified users to create a budget", function() {
    $user = User::factory()->create([
        "email_verified_at" => null,
    ]);

    $response = $this->actingAs($user)
        ->post(route("budgets.store"), [
            "name" => "Travel to Asia",
            "amount" => 1000,
            "type" => "goal",
        ]);

    $response->assertRedirect(route("verification.notice"));
});

it("validates amount must be greater than zero", function() {
    $user = User::factory()->create([
        "email_verified_at" => now(),
    ]);

    $response = $this->actingAs($user)
        ->from(route("budgets.create"))
        ->post(route("budgets.store"), [
            "name" => "Tacos",
            "amount" => -10,
            "type" => "general",
        ]);

    $response->assertRedirect(route("budgets.create"));

    $response->assertSessionHasErrors([ "amount" ]);
});

it("validates type must be valid", function() {
    $user = User::factory()->create([
        "email_verified_at" => now(),
    ]);

    $response = $this->actingAs($user)
        ->from(route("budgets.create"))
        ->post(route("budgets.store"), [
            "name" => "Tacos",
            "amount" => 110,
            "type" => "not_valid",
        ]);

    $response->assertRedirect(route("budgets.create"));

    $response->assertSessionHasErrors([ "type" ]);
});

it("accepts valid budget types", function() {
    $user = User::factory()->create([
        "email_verified_at" => now(),
    ]);

    $response = $this->actingAs($user)
        ->post(route("budgets.store"), [
            "name" => "Tacos",
            "amount" => 110,
            "type" => "goal",
        ]);

    $response->assertSessionDoesntHaveErrors();

    $this->assertDatabaseHas("budgets", ["type" => "goal",]);
});

