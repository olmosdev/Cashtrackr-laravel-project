<?php
// Using Pest

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class); // To make migrations and create tables before each test 

// You can use it() or test()
it("shows the registration screen", function() {
    // "this" refers to the instance that is running the test
    $response = $this->get(route("register"));
    // dd($response);

    $response->assertOk();
    $response->assertStatus(200);
    $response->assertSee("Crear Cuenta");
    $response->assertSee("Registrarme");
    $response->assertSeeInOrder([
        "Crear Cuenta",
        "Registrarme"
    ]);
});

it("registers a new user as unverified and dispatches the registered event", function()  {

    Event::fake(); // To simulate behavior

    $response = $this->post(route("register.store"), [
        "name" => "Sarah Lim",
        "email" => "sar@example.com",
        "password" => "Uwu@54sar",
        "password_confirmation" => "Uwu@54sar"
    ]);
    // dd($response);

    $response->assertRedirect(route("verification.notice"));

    $user = User::where("email", "sar@example.com")->first();

    expect($user)->not()->toBeNull();
    expect($user->name)->toBe("Sarah Lim");
    expect($user->email)->toBe("sar@example.com");
    expect($user->hasVerifiedEmail())->toBeFalse();

    Event::assertDispatched(Registered::class);
});

it("should validate required fields when the request body is empty", function() {
    $response = $this->post(route("register.store"), []);

    $response->assertSessionHasErrors([
        "name",
        "email",
        "password"
    ]);

    $response->assertSessionHasErrors([
        "name" => "El Nombre es obligatorio",
        "email" => "El E-mail es obligatorio",
        "password" => "La Contraseña es obligatoria"
    ]);
});

// Factories allow you to automatically add the values ​​required by your models (Generate random values)
it("prevents duplicate email adresses", function() {

    // This factory already comes by default in Laravel
    User::factory()->create([
        "email" => "sar@example.com"
    ]);

    $response = $this->post(route("register.store"), [
        "name" => "Sarah Lim",
        "email" => "sar@example.com",
        "password" => "Uwu@54sar",
        "password_confirmation" => "Uwu@54sar"
    ]);

    $response->assertRedirect();

    $response->assertSessionHasErrors([
        "email" => "Este correo ya ha sido registrado",
    ]);
});

it("sends the verification email notification after registration", function() {
    Notification::fake(); // To simulate behavior

    $response = $this->post(route("register.store"), [
        "name" => "Sarah Lim",
        "email" => "sar@example.com",
        "password" => "Uwu@54sar",
        "password_confirmation" => "Uwu@54sar"
    ]);

    $user = User::where("email", "sar@example.com")->first();


    Notification::assertSentTo($user, VerifyEmail::class);
});

it("verifies the user email from a signed verification link", function() {

    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]
    );

    $response = $this->actingAs($user)->get($verificationUrl);
    $response->assertRedirect(route("dashboard"));
    expect($user->hasVerifiedEmail())->toBeTrue();
});

it("does not allow an unverified user to access the dashboard", function() {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route("dashboard"));
    $response->assertRedirect(route("verification.notice"));
});

it("allows a verified user to access the dashboard", function() {
    $user = User::factory()->create([
        "email_verified_at" => now()
    ]);

    $response = $this->actingAs($user)->get(route("dashboard"));
    $response->assertOk();
});

