<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it("shows the login screen", function() {
    $response = $this->get(route("login"));

    $response->assertOk();
});

it("logs in a verified user successfully", function() {
    // $user = User::factory()->create([
    //     "email" => "elisa@example.com",
    //     "password" => "@Lipocal777Mine",
    //     "email_verified_at" => now(),
    // ]);
    // dd($user);

    User::factory()->create([
        "email" => "elisa@example.com",
        "password" => "@Lipocal777Mine",
        "email_verified_at" => now(),
    ]);

    $response = $this->post(route("login.store"), [
        "email" => "elisa@example.com",
        "password" => "@Lipocal777Mine",
    ]);

    $response->assertRedirect(route("dashboard"));
    $this->assertAuthenticated();
});

it("does not log in with invalid credentials", function() {
    User::factory()->create([
        "email" => "elisa@example.com",
        "password" => "@Lipocal777Mine",
    ]);

    $response = $this->from(route("login"))->post(route("login.store"), [
        "email" => "elisa@example.com",
        "password" => "@dramamine",
    ]);

    $response->assertRedirect(route("login"));
    $response->assertSessionHas("error", "Las credenciales que ingresaste no son correctas");
    $this->assertGuest();
});

it("prevents unverified user from accessing dashboard", function() {

    User::factory()->unverified()->create([
        "email" => "elisa@example.com",
        "password" => "@Lipocal777Mine",
    ]);

    $response = $this->post(route("login.store"), [
        "email" => "elisa@example.com",
        "password" => "@Lipocal777Mine",
    ]);

    $response->assertRedirect(route("dashboard"));
    $this->assertAuthenticated();

    $dashboardResponse = $this->get(route("dashboard"));
    $dashboardResponse->assertRedirect(route("verification.notice"));
});

it("does not allow access to dashboard if email is not verified", function() {
    $user = User::factory()->create([
        "password" => "@Bleybley6724",
        "email_verified_at" => null,
    ]);

    $response = $this->actingAs($user)->get(route("dashboard"));

    $response->assertRedirect(route("verification.notice"));
});

it("allows access to dashboard if email is verified", function() {
    $user = User::factory()->create([
        "password" => "@Bleybley6724",
        "email_verified_at" => now(),
    ]);

    $response = $this->actingAs($user)->get(route("dashboard"));

    $response->assertOk();
});

it("fails login if user does not exist", function() {
    $response = $this->from(route("login"))->post(route("login.store"), [
        "email" => "pweir@example.com",
        "password" => "@Dsdasaaf3234",
    ]);

    $response->assertRedirect(route("login"));
    $response->assertSessionHasErrors([
        "email" => "No encontramos una cuenta con ese correo electrónico"
    ]);
    $this->assertGuest();
});

