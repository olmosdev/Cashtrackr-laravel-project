<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    // Expected function (All the time)
    public function index()
    {
        return view("auth.register");
    }

    // We call this function when an user send info through the form (laravel convention)
    public function store(/*Request $request*/ SignupRequest $request)
    {
        // $name = $request->input("name");
        // $email = $request->input("email");
        // return "Hi " . $name . " Email " . $email;

        // Moved to SignupRequest.php
        // $data = $request->validate([
        //     // "name" => ["required", "string"],
        //     // "email" => ["required", "email"]
        // ], [
        //     // Custom error messages
        //     "name.required" => "El Nombre es obligatorio",
        //     "email.required" => "El E-mail es obligatorio",
        //     "email.email" => "E-mail no valido"
        // ]);
        $data = $request->validated();

        // Special laravel function (It will stop code execution and send to the screen an array, object or a variable)
        // dd($data);
        $user = User::create($data);

        // Auth Event
        event(new Registered($user));

        // Authenticating the user (logging in and creating a cookie)
        Auth::login($user);

        // Redirecting the user to the page where they must verify their account
        return redirect()->route("verification.notice");
    }
}
