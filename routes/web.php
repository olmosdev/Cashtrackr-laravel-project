<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\LogoutController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Closure sintax
// Route::get("/auth/register", function() {
//     return view("auth.register");
// })->name("register");

// Route::get("/auth/login", function() {
//     return view("auth/login");
// })->name("login");

// Controller Sintax
Route::get("/auth/register", [RegisterController::class, "index"])->name("register");
Route::post("/auth/register", [RegisterController::class, "store"])->name("register.store");

Route::get("/auth/login", [LoginController::class, "index"])->name("login");
Route::post("/auth/login", [LoginController::class, "store"])->name("login.store");

// Whenever there is a form for a resource, "store" will always receive that request.
Route::post("/auth/logout", [LogoutController::class, "store"])->name("logout.store");

// To confirm account
// This route requires that a user be authenticated in order to then verify the user's email.
Route::get("/email/verify/{id}/{hash}", function(EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()
            ->route("dashboard")
            ->with("success", "Tu cuenta ha sido verificada exitosamente. Ya puedes crear Presupuestos y Gastos"); // Temporary message (The message is retrieved with a session). It only appears once and disappears in seconds.
})
    ->middleware([
        "auth", // This tells Laravel that the user must be authenticated
        "signed" // Verifying that the integrity of the hash has not been modified
    ]) // Retrieving the cookie
    ->name("verification.verify");

// To avoid creating a controller, we use closures
Route::get("/email/verify", function() {
    return view("auth.verify-email");
})
    ->middleware("auth") // Protecting the URL so that only the account creator can access the page
    ->name("verification.notice"); 

Route::post("/email/verification-notification", function(Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with("success", "Correo de verificación reenviado. Revisa tu bandeja de entrada.");
})
    ->middleware([
        "auth",
        "throttle:2,1" // This middleware limits the number of requests to 2 per minute. It prevents abuse of the resend verification email feature.
    ])
    ->name("verification.send");

// Route::get("/dashboard", function() {
//     return view("dashboard");
// })
//     ->middleware([
//         "auth",
//         "verified" // The user must have verified their account
//     ])
//     ->name("dashboard");

// Migrated from Closure to Controller
// Route::get("/dashboard", [BudgetController::class, "index"])
//     ->middleware([
//         "auth",
//         "verified" // The user must have verified their account
//     ])
//     ->name("dashboard");
// Route::get("/budgets/create", [BudgetController::class, "create"])->middleware(["auth", "verified"])->name("budgets.create");

Route::middleware(["auth", "verified"])->prefix("dashboard")->group(function() {
    Route::get("/", [BudgetController::class, "index"])->name("dashboard");
    Route::get("/budgets/create", [BudgetController::class, "create"])->name("budgets.create");
    Route::post("/budgets", [BudgetController::class, "store"])->name("budgets.store");
});

