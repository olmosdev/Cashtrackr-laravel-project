<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignInRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view("auth/login");
    }

    public function store(SignInRequest $request)
    {
        $data = $request->validated();

        if (!Auth::attempt($data, true)) { // We used "true" to be always authenticated
            // Returning with a new session
            return back()->with("error", "Las credenciales que ingresaste no son correctas");
        }

        return redirect()->route("dashboard");
    }
}
